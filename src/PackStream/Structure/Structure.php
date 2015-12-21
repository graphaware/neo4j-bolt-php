<?php

namespace GraphAware\Bolt\PackStream\Structure;

class Structure
{
    private $signature;

    private $elements = [];

    private $size = 0;

    public function __construct($signature, $size)
    {
        $this->signature = $signature;
        $this->size = (int) $size;
    }

    public function addElement($elt)
    {
        $this->elements[] = $elt;
    }

    public function setElements($elts)
    {
        $this->elements = $elts;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @return array
     */
    public function getElements()
    {
        return $this->elements[0];
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }


    public function getValue()
    {
        return $this->elements;
    }
}