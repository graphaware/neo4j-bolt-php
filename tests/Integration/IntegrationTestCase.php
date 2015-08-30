<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Bolt;

abstract class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Bolt\Bolt
     */
    protected $driver;

    public function setUp()
    {
        $this->driver = new Bolt('localhost', 7687);
    }

    /**
     * @return \GraphAware\Bolt\Bolt
     */
    public function getDriver()
    {
        return $this->driver;
    }
}