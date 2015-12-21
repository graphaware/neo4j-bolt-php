<?php

namespace GraphAware\Bolt\PackStream\Structure;

class ListStructure implements StructureInterface, \ArrayAccess
{
    private $size;

    private $elements = [];

    public function __construct($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    public function addElement($element)
    {
        $this->elements[] = $element;
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->elements);
    }

    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->elements[$offset] : null;
    }

    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->elements[] = $value;
        } else {
            $this->elements[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->elements[$offset]);
    }
}