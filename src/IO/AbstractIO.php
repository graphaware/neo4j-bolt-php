<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\IO;

use PTS\Bolt\Exception\IOException;

abstract class AbstractIO implements IoInterface
{
    /**
     * @return bool
     *
     * @throws IOException
     */
    public function assertConnected()
    {
        if (!$this->isConnected()) {
            return $this->connect();
        }

        return true;
    }
}
