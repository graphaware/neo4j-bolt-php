<?php

namespace GraphAware\Bolt\Protocol\Message;

use GraphAware\Bolt\Protocol\Constants;

class PullAllMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'PULL_ALL';

    public function __construct()
    {
        parent::__construct(Constants::SIGNATURE_PULL_ALL);
    }

    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }
}