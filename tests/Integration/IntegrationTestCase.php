<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Driver;

abstract class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Bolt\Driver
     */
    protected $driver;

    public function setUp()
    {
        $this->driver = new Driver('localhost', 7687);
    }

    /**
     * @return \GraphAware\Bolt\Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }
}