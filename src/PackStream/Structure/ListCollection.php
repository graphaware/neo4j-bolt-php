<?php

namespace GraphAware\Bolt\PackStream\Structure;

use Doctrine\Common\Collections\ArrayCollection;

class ListCollection extends AbstractElement
{
    protected $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function add(AbstractElement $element)
    {
        $this->elements->add($element);
    }

    public function getElements()
    {
        return $this->elements;
    }
}