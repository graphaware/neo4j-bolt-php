<?php

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

    public function isCompleted()
    {
        return $this->completed;
    }
}