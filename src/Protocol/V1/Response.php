<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\V1;

class Response
{
    protected $completed = false;

    protected $records = [];

    protected $metadata = [];

    public function onSuccess($metadata)
    {
        $this->completed = true;
        $this->metadata[] = $metadata;
    }

    public function onRecord($metadata)
    {
        $this->records[] = $metadata;
    }

    public function getRecords()
    {
        return $this->records;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    public function isCompleted()
    {
        return $this->completed;
    }
}