<?php

namespace GraphAware\Bolt\Protocol\Message;

class RawMessage
{
    protected $bytes = '';

    public function __construct($bytes)
    {
        $this->bytes = $bytes;
    }

    public function getLength()
    {
        return mb_strlen($this->bytes, 'ASCII');
    }

    public function getBytes()
    {
        return $this->bytes;
    }
}