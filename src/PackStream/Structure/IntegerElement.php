<?php

namespace GraphAware\Bolt\PackStream\Structure;

class IntegerElement extends AbstractElement
{
    protected $value;

    public function __construct($value)
    {
        $this->value = (int) $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}