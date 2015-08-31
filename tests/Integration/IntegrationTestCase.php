<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Driver;
use Neoxygen\NeoClient\ClientBuilder;

abstract class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Bolt\Driver
     */
    protected $driver;

    /**
     * @var \Neoxygen\NeoClient\Client
     */
    protected $client;

    public function setUp()
    {
        $this->driver = new Driver('localhost', 7687);
        $this->client = ClientBuilder::create()
            ->addConnection('default', 'http', 'localhost', 7474)
            ->setAutoFormatResponse(true)
            ->build();
    }

    /**
     * @return \GraphAware\Bolt\Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return \Neoxygen\NeoClient\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    public function emptyDB()
    {
        $q = 'MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE r,n';
        $this->client->sendCypherQuery($q);
    }
}