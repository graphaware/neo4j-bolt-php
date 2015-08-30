<?php

namespace GraphAware\Bolt\Protocol\Message;

use GraphAware\Bolt\PackStream\Structure\Map;
use GraphAware\Bolt\Protocol\Constants;

class SuccessMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'SUCCESS';

    protected $map;

    public function __construct(Map $map)
    {
        parent::__construct(Constants::SIGNATURE_SUCCESS);
        $this->map = $map;
    }

    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

    public function getFields()
    {
        return $this->map->get('fields');
    }

    public function hasFields()
    {
        return !$this->map->isEmpty();
    }
}