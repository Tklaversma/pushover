<?php

namespace NotificationChannels\Pushover;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Notifications\Notification;
use NotificationChannels\Pushover\Exceptions\CouldNotSendNotification;
use NotificationChannels\Pushover\Exceptions\ServiceCommunicationError;

class PushoverChannel
{
    /** @var Pushover */
    protected $pushover;

    /** @var Dispatcher */
    protected $events;

    /**
     * Create a new Pushover channel instance.
     *
     * @param Pushover   $pushover
     * @param Dispatcher $events
     */
    public function __construct(Pushover $pushover, Dispatcher $events)
    {
        $this->pushover = $pushover;
        $this->events   = $events;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     *
     * @return void
     * @throws CouldNotSendNotification
     * @throws GuzzleException
     */
    public function send($notifiable, Notification $notification): void
    {
        if (!$pushoverReceiver = $notifiable->routeNotificationFor('pushover')) {
            return;
        }

        if (is_string($pushoverReceiver)) {
            $pushoverReceiver = PushoverReceiver::withUserKey($pushoverReceiver);
        }

        $message = $notification->toPushover($notifiable);

        try {
            $this->pushover->send(array_merge($message->toArray(), $pushoverReceiver->toArray()));
        } catch (ServiceCommunicationError $serviceCommunicationError) {
            $this->fireFailedEvent($notifiable, $notification, $serviceCommunicationError->getMessage());
        }
    }

    /**
     * @param $notifiable
     * @param $notification
     * @param $message
     *
     * @return void
     */
    protected function fireFailedEvent($notifiable, $notification, $message): void
    {
        $this->events->fire(
            new NotificationFailed($notifiable, $notification, 'pushover', [$message])
        );
    }
}
