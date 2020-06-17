<?php

namespace PTS\Bolt\Tests\Result;

use PTS\Bolt\Result\Type\MapAccess;

class DummyMA extends MapAccess
{
    public function __construct(array $properties)
    {
        $this->properties = $properties;
    }
}
