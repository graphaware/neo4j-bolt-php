<?php


namespace PTS\Bolt\Protocol\V2;

class Session extends \PTS\Bolt\Protocol\V1\Session
{
    const PROTOCOL_VERSION = 2;

    /**
     * {@inheritdoc}
     */
    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }
}
