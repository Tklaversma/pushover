<?php

namespace NotificationChannels\Pushover;

use Carbon\Carbon;
use NotificationChannels\Pushover\Exceptions\EmergencyNotificationRequiresRetryAndExpire;

class PushoverMessage
{
    /**
     * The text content of the message.
     *
     * @var string
     */
    public $content;

    /**
     * The (optional) title of the message.
     *
     * @var string
     */
    public $title;

    /**
     * The (optional) timestamp of the message.
     *
     * @var int
     */
    public $timestamp;

    /**
     * The (optional) priority of the message.
     *
     * @var int
     */
    public $priority;

    /**
     * The (optional) timeout between retries when sending a message
     * with an emergency priority. The timeout is in seconds.
     *
     * @var int
     */
    public $retry;

    /**
     * The (optional) expire time of a message with an emergency priority.
     * The expire time is in seconds.
     *
     * @var int
     */
    public $expire;

    /**
     * The (optional) supplementary url of the message.
     *
     * @var string
     */
    public $url;

    /**
     * The (optional) supplementary url title of the message.
     *
     * @var string
     */
    public $urlTitle;

    /**
     * The (optional) sound of the message.
     *
     * @var string
     */
    public $sound;

    /**
     * Message priorities.
     */
    public const LOWEST_PRIORITY    = -2;
    public const LOW_PRIORITY       = -1;
    public const NORMAL_PRIORITY    = 0;
    public const HIGH_PRIORITY      = 1;
    public const EMERGENCY_PRIORITY = 2;

    /**
     * @param string $content
     */
    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    /**
     * @param string $content
     *
     * @return static
     */
    public static function create(string $content = '')
    {
        return new static($content);
    }

    /**
     * Set the content of the Pushover message.
     *
     * @param string $content
     *
     * @return $this
     */
    public function content(string $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Set the title of the Pushover message.
     *
     * @param string $title
     *
     * @return $this
     */
    public function title(string $title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Set the time of the Pushover message.
     *
     * @param Carbon|int $time
     *
     * @return $this
     */
    public function time($time)
    {
        if ($time instanceof Carbon) {
            $time = $time->timestamp;
        }

        $this->timestamp = $time;

        return $this;
    }

    /**
     * Set a supplementary url for the Pushover message.
     *
     * @param string      $url
     * @param string|null $title
     *
     * @return $this
     */
    public function url(string $url, ?string $title = null)
    {
        $this->url      = $url;
        $this->urlTitle = $title;

        return $this;
    }

    /**
     * Set the sound of the Pushover message.
     *
     * @param string $sound
     *
     * @return $this
     */
    public function sound(string $sound)
    {
        $this->sound = $sound;

        return $this;
    }

    /**
     * Set the priority of the Pushover message.
     * Retry and expire are mandatory when setting the priority to emergency.
     *
     * @param int      $priority
     * @param int|null $retryTimeout
     * @param int|null $expireAfter
     *
     * @return $this
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function priority(int $priority, ?int $retryTimeout = null, ?int $expireAfter = null)
    {
        $this->noEmergencyWithoutRetryOrExpire($priority, $retryTimeout, $expireAfter);

        $this->priority = $priority;
        $this->retry    = $retryTimeout;
        $this->expire   = $expireAfter;

        return $this;
    }

    /**
     * Set the priority of the Pushover message to the lowest priority.
     *
     * @return $this
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function lowestPriority()
    {
        return $this->priority(self::LOWEST_PRIORITY);
    }

    /**
     * Set the priority of the Pushover message to low.
     *
     * @return $this
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function lowPriority()
    {
        return $this->priority(self::LOW_PRIORITY);
    }

    /**
     * Set the priority of the Pushover message to normal.
     *
     * @return $this
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function normalPriority()
    {
        return $this->priority(self::NORMAL_PRIORITY);
    }

    /**
     * Set the priority of the Pushover message to high.
     *
     * @return $this
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function highPriority()
    {
        return $this->priority(self::HIGH_PRIORITY);
    }

    /**
     * Set the priority of the Pushover message to emergency.
     * Retry and expire are mandatory when setting the priority to emergency.
     *
     * @param $retryTimeout
     * @param $expireAfter
     *
     * @return $this
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    public function emergencyPriority($retryTimeout, $expireAfter)
    {
        return $this->priority(self::EMERGENCY_PRIORITY, $retryTimeout, $expireAfter);
    }

    /**
     * Array representation of Pushover Message.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'message'   => $this->content,
            'title'     => $this->title,
            'timestamp' => $this->timestamp,
            'priority'  => $this->priority,
            'url'       => $this->url,
            'url_title' => $this->urlTitle,
            'sound'     => $this->sound,
            'retry'     => $this->retry,
            'expire'    => $this->expire,
        ];
    }

    /**
     * Ensure an emergency message has an retry and expiry time.
     *
     * @param int $priority
     * @param int $retry
     * @param int $expire
     *
     * @throws EmergencyNotificationRequiresRetryAndExpire
     */
    protected function noEmergencyWithoutRetryOrExpire($priority, $retry, $expire)
    {
        if ($priority == self::EMERGENCY_PRIORITY && (!isset($retry) || !isset($expire))) {
            throw new EmergencyNotificationRequiresRetryAndExpire();
        }
    }
}
