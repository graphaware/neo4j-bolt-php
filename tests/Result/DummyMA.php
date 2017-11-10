<?php

namespace GraphAware\Bolt\Tests\Result;

use GraphAware\Bolt\Result\Type\MapAccess;

class DummyMA extends MapAccess
{
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }
}
