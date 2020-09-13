<?php

namespace NotificationChannels\Pushover;

class PushoverReceiver
{
    protected $key;
    protected $token;
    protected $devices = [];

    /**
     * PushoverReceiver constructor.
     *
     * @param string $key User or group key.
     */
    protected function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Create new Pushover receiver with an user key.
     *
     * @param string $userKey Pushover user key.
     *
     * @return PushoverReceiver
     */
    public static function withUserKey(string $userKey)
    {
        return new static($userKey);
    }

    /**
     * Create new Pushover receiver with a group key.
     *
     * @param string $groupKey Pushover group key.
     *
     * @return PushoverReceiver
     */
    public static function withGroupKey(string $groupKey)
    {
        // This has exactly the same behaviour as an user key, so we
        // will use the same factory method as for the user key.
        return self::withUserKey($groupKey);
    }

    /**
     * Send the message to a specific device.
     *
     * @param array|string $device
     *
     * @return PushoverReceiver
     */
    public function toDevice($device): PushoverReceiver
    {
        if (is_array($device)) {
            $this->devices = array_merge($device, $this->devices);

            return $this;
        }

        $this->devices[] = $device;

        return $this;
    }

    /**
     * Set the application token.
     *
     * @param string $token
     *
     * @return PushoverReceiver
     */
    public function withApplicationToken(string $token): PushoverReceiver
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get array representation of Pushover receiver.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            [
                'user'   => $this->key,
                'device' => implode(',', $this->devices),
            ],
            $this->token ? ['token' => $this->token] : []
        );
    }
}
