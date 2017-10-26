<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\GraphDatabase;

abstract class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Bolt\Driver
     */
    protected $driver;

    protected function setUp()
    {
        $this->driver = GraphDatabase::driver("bolt://localhost");
    }

    /**
     * @return \GraphAware\Bolt\Driver
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return \Graphaware\Bolt\Protocol\SessionInterface
     */
    protected function getSession()
    {
        return $this->driver->session();
    }

    /**
     * Empty the database
     */
    public function emptyDB()
    {
        $this->driver->session()->run('MATCH (n) DETACH DELETE n');
    }
}