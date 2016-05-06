<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\PackStream\Structure;

class MessageStructure
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var array
     */
    protected $elements;

    /**
     * @param string $signature
     * @param int    $size
     */
    public function __construct($signature, $size)
    {
        $this->signature = $signature;
        $this->size = $size;
    }

    /**
     * @param $element
     */
    public function addElement($element)
    {
        $this->elements[] = $element;
    }

    /**
     * @return mixed
     */
    public function getElements()
    {
        return $this->elements[0];
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
    public function isFailure()
    {
        return 'FAILURE' === $this->signature;
    }

    /**
     * @return bool
     */
    public function isIgnored()
    {
        return 'IGNORED' === $this->signature;
    }

    /**
     * @return bool
     */
    public function isRecord()
    {
        return 'RECORD' === $this->signature;
    }
}
