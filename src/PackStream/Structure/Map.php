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

    public function getValue()
    {
        return $this->elements;
    }

    public function toArray()
    {
        $arr = [];
        foreach ($this->elements as $k => $v) {
            $arr[$k] = $v->getValue();
        }

        return $arr;
    }
}