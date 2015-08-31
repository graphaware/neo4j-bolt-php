<?php

namespace GraphAware\Bolt\Tests\IO;

use GraphAware\Bolt\IO\AbstractIO;

class IOMock extends AbstractIO
{
    protected $messages;

    public function __construct($host = null, $port = null)
    {
        $this->messages = '';
    }
}

