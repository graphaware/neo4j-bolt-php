<?php

namespace GraphAware\Bolt\Protocol\Message;

use GraphAware\Bolt\Protocol\Constants;

abstract class AbstractMessage implements MessageInterface
{
    protected $signature;

    protected $fields = [];

    protected $isSerialized = false;

    protected $serialization = null;

    public function __construct($signature, array $fields = array())
    {
        $this->signature = $signature;
        $this->fields = $fields;
    }

    public function getSignature()
    {
        return $this->signature;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getFieldsLength()
    {
        return count($this->fields);
    }

    public function setSerialization($stream)
    {
        $this->serialization = $stream;
        $this->isSerialized = true;
    }

    public function getSerialization()
    {
        return $this->serialization;
    }

    public function isSuccess()
    {
        return $this->getMessageType() === 'SUCCESS';
    }

    public function isFailure()
    {
        return $this->getMessageType() === 'FAILURE';
    }

    public function isRecord()
    {
        return $this->getMessageType() === 'RECORD';
    }
}