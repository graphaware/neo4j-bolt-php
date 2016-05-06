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

use GraphAware\Bolt\Exception\MessageFailureException;

class Response
{
    /**
     * @var bool
     */
    protected $completed = false;

    /**
     * @var array
     */
    protected $records = [];

    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @param $metadata
     */
    public function onSuccess($metadata)
    {
        $this->completed = true;
        $this->metadata[] = $metadata;
    }

    /**
     * @param $metadata
     */
    public function onRecord($metadata)
    {
        $this->records[] = $metadata;
    }

    /**
     * @return array
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @param $metadata
     *
     * @throws MessageFailureException
     */
    public function onFailure($metadata)
    {
        $this->completed = true;
        $e = new MessageFailureException($metadata->getElements()['message']);
        $e->setStatusCode($metadata->getElements()['code']);

        throw $e;
    }

    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @return bool
     */
    public function isCompleted()
    {
        return $this->completed;
    }
}
