<?php

namespace GraphAware\Bolt\Protocol\Message;

use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\PackStream\Structure\Map;

class FailureMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'FAILURE';

    protected $code;

    protected $message;

    public function __construct(Map $map)
    {
        parent::__construct(Constants::SIGNATURE_FAILURE);
        $this->code = $map->get('code')->__toString();
        $this->message = $map->get('message')->__toString();
    }

    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }


}