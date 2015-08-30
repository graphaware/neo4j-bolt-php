<?php

namespace GraphAware\Bolt\PackStream\Structure;

class Map extends AbstractElement
{
    protected $elements;

    protected $size;

    public function __construct($size, array $elements = array())
    {
        $this->size = $size;
        $this->elements = $elements;
    }

    public function set($key, $value)
    {
        $this->elements[$key] = $value;
    }

    public function get($key)
    {
        if (array_key_exists($key, $this->elements)) {
            return $this->elements[$key];
        }

        return null;
    }

    public function contains($element)
    {
        if (in_array($element, $this->elements)) {
            return true;
        }

        return false;
    }

    public function isEmpty()
    {
        return 0 === $this->size;
    }

    public function getSize()
    {
        return $this->size;
    }
}