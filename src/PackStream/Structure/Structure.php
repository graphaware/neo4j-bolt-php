<?php

namespace GraphAware\Bolt\PackStream\Structure;

class Structure
{
    /**
     * @var string
     */
    private $signature;

    /**
     * @var array
     */
    private $elements = [];

    /**
     * @var int
     */
    private $size = 0;

    public function __construct($signature, $size)
    {
        $this->signature = $signature;
        $this->size = (int) $size;
    }

    /**
     * @param $elt
     */
    public function addElement($elt)
    {
        $this->elements[] = $elt;
    }

    /**
     * @param array $elts
     */
    public function setElements($elts)
    {
        $this->elements = $elts;
    }

    /**
     * @return string
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
        if (in_array($this->signature, $this->types())) {
            return $this->elements;
        }

        return $this->elements[0];
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @return array
     */
    public function getValue()
    {
        return $this->elements;
    }

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return 'SUCCESS' === $this->signature;
    }

    /**
     * @return bool
     */
    public function isRecord()
    {
        return 'RECORD' === $this->signature;
    }

    /**
     * @return bool
     */
    public function isFailure()
    {
        return 'FAILURE' === $this->signature;
    }

    /**
     * @return bool
     */
    public function hasFields()
    {
        return array_key_exists('fields', $this->getElements());
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->hasFields() ? $this->getElements()['fields'] : [];
    }

    /**
     * @return bool
     */
    public function hasStatistics()
    {
        return array_key_exists('stats', $this->getElements());
    }

    /**
     * @return array
     */
    public function getStatistics()
    {
        return $this->hasStatistics() ? $this->getElements()['stats'] : [];
    }

    /**
     * @return bool
     */
    public function hasType()
    {
        return array_key_exists('type', $this->getElements());
    }

    /**
     * @return array
     */
    public function getType()
    {
        return $this->hasType() ? $this->getElements()['type'] : [];
    }

    /**
     * @return array
     */
    private function types()
    {
        return ['NODE', 'RELATIONSHIP', 'PATH', 'UNBOUND_RELATIONSHIP'];
    }
}
