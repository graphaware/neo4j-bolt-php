<?php

namespace GraphAware\Bolt\Tests\TCK;

use GraphAware\Bolt\Driver;

class TCKTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Bolt\Driver
     */
    private $driver;

    public function setUp()
    {
        $this->driver = new Driver('localhost', 7687);
    }

    /**
     * @return \GraphAware\Bolt\Driver
     */
    protected function getDriver()
    {
        return $this->driver;
    }
}