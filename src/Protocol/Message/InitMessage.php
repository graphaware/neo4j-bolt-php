<?php

namespace GraphAware\Bolt\Protocol\Message;

use GraphAware\Bolt\Protocol\Constants;

class InitMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'INIT';

    public function __construct($userAgent)
    {
        parent::__construct(Constants::SIGNATURE_INIT, array($userAgent));
    }

    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }
}