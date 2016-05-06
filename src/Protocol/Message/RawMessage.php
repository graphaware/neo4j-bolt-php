<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\Message;

class RawMessage
{
    protected $bytes = '';

    /**
     * @param string $bytes
     */
    public function __construct($bytes)
    {
        $this->bytes = $bytes;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return mb_strlen($this->bytes, 'ASCII');
    }

    /**
     * @return string
     */
    public function getBytes()
    {
        return $this->bytes;
    }
}
