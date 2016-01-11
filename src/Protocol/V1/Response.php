<?php

namespace GraphAware\Bolt\Protocol\V1;

class Response
{
    const ON_SUCCESS = 'SUCCESS';

    const ON_FAILURE = 'FAILURE';

    const ON_IGNORED = 'IGNORED';

    const ON_RECORD = 'RECORD';

    protected $callbacks = [];

    protected $isCompleted = false;

    public function registerCallback($eventName, $callback)
    {
        $this->callbacks[$eventName][] = $callback;
    }

    public function onSuccess($values)
    {
        $this->doDispatch(self::ON_SUCCESS, $values);
    }

    public function onFailure($values)
    {
        $this->doDispatch(self::ON_FAILURE, $values);
    }

    public function onIgnored($values)
    {
        $this->doDispatch(self::ON_IGNORED, $values);
    }

    public function onRecord($values)
    {
        $this->doDispatch(self::ON_RECORD, $values);
    }

    public function doDispatch($eventName, $values)
    {
        foreach ($this->callbacks[$eventName] as $callback) {
            call_user_func($callback, $values);
        }
    }

    public function setCompleted()
    {
        $this->isCompleted = true;
    }

    public function isCompleted()
    {
        return $this->isCompleted;
    }
}