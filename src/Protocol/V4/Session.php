<?php


namespace PTS\Bolt\Protocol\V4;


use PTS\Bolt\Protocol\Message\V4\PullMessage;

class Session extends \PTS\Bolt\Protocol\V3\Session
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
        // same effect as PullAll message
        return new PullMessage(['n' => -1]);
    }




}