<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\IO;

use GraphAware\Bolt\Exception\IOException;

interface IoInterface
{
    /**
     * @param string $data
     *
     * @throws IOException
     */
    public function write($data);

    /**
     * @param int $n
     *
     * @return string
     *
     * @throws IOException
     */
    public function read($n);

    /**
     * @param int $sec
     * @param int $usec
     *
     * @return int
     */
    public function select($sec, $usec);

    /**
     * @return bool
     *
     * @throws IOException
     */
    public function connect();

    /**
     * @return bool
     *
     * @throws IOException
     */
    public function reconnect();

    /**
     * @return bool
     */
    public function isConnected();

    /**
     * @return bool
     */
    public function close();
}
