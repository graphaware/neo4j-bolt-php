<?php

namespace PTS\Bolt\PackStream\Structure;

class Structure
{
    const SIGNATURE_UNBOUND_RELATIONSHIP = 'UNBOUND_RELATIONSHIP';
    const SIGNATURE_NODE = 'NODE';
    const SIGNATURE_PATH = 'PATH';
    const SIGNATURE_RELATIONSHIP = 'RELATIONSHIP';
    const SIGNATURE_POINT2D = 'POINT2D';
    const SIGNATURE_POINT3D = 'POINT3D';
    const SIGNATURE_DATE = 'DATE';
    const SIGNATURE_DATE_TIME_OFFSET = 'DATE_TIME_OFFSET';
    const SIGNATURE_DATE_TIME_ZONED = 'DATE_TIME_ZONED';
    const SIGNATURE_LOCAL_DATE_TIME = 'LOCAL_DATE_TIME';
    const SIGNATURE_LOCAL_TIME = 'LOCAL_TIME';
    const SIGNATURE_TIME = 'TIME';
    const SIGNATURE_DURATION = 'DURATION';

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
        $this->size = (int)$size;
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
        return [
            self::SIGNATURE_NODE,
            self::SIGNATURE_RELATIONSHIP,
            self::SIGNATURE_PATH,
            self::SIGNATURE_UNBOUND_RELATIONSHIP,
            self::SIGNATURE_POINT2D,
            self::SIGNATURE_POINT3D,
            self::SIGNATURE_DATE,
            self::SIGNATURE_DATE_TIME_OFFSET,
            self::SIGNATURE_DATE_TIME_ZONED,
            self::SIGNATURE_LOCAL_DATE_TIME,
            self::SIGNATURE_LOCAL_TIME,
            self::SIGNATURE_TIME,
            self::SIGNATURE_DURATION,
        ];
    }
}
