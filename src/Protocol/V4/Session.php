<?php


namespace GraphAware\Bolt\Protocol\V4;


use GraphAware\Bolt\Protocol\Message\V4\PullMessage;

class Session extends \GraphAware\Bolt\Protocol\V3\Session
{
    const PROTOCOL_VERSION = 4;

    /**
     * {@inheritdoc}
     */
    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }

    protected function createPullAllMessage()
    {
        return new PullMessage(['n' => -1]);
    }




}