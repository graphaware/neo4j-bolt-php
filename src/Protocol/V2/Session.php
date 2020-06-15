<?php


namespace GraphAware\Bolt\Protocol\V2;

class Session extends \GraphAware\Bolt\Protocol\V1\Session
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