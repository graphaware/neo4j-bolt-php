<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Record;

use GraphAware\Common\Result\RecordViewInterface;

class RecordView implements RecordViewInterface
{
    /**
     * @var array
     */
    protected $keys = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $keyToIndexMap = [];

    /**
     * @return array
     */
    public function keys()
    {
        return $this->keys;
    }

    /**
     * RecordView constructor.
     * @param array $keys
     * @param array $values
     */
    public function __construct(array $keys, array $values)
    {
        $this->keys = $keys;
        $this->values = $values;
        foreach ($this->keys as $i => $k) {
            $this->keyToIndexMap[$k] = $i;
        }
    }

    /**
     * @return bool
     */
    public function hasValues()
    {
        return !empty($this->values);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function value($key)
    {
        return $this->values[$this->keyToIndexMap[$key]];
    }

    /**
     * @return array
     */
    public function values()
    {
        return $this->values;
    }

    /**
     * @param $index
     * @return mixed
     */
    public function valueByIndex($index)
    {
        return $this->values[$index];
    }

    /**
     * @return \GraphAware\Bolt\Record\RecordView
     */
    public function record()
    {
        return clone($this);
    }

}