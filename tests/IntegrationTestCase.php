<?php

namespace GraphAware\Bolt\Tests;

use GraphAware\Bolt\Configuration;
use GraphAware\Bolt\GraphDatabase;

class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Bolt\Driver
     */
    protected $driver;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $this->driver = GraphDatabase::driver(
            $this->getBoltUrl(),
            getenv('NEO4J_USER') ?
                Configuration::create()->withCredentials(getenv('NEO4J_USER'), getenv('NEO4J_PASSWORD'))
                : Configuration::create()
        );
    }

    /**
     * @return string
     */
    protected function getBoltUrl(){
        $boltUrl = 'bolt://localhost';
        if (getenv('NEO4J_HOST')) {
            $boltUrl = sprintf(
                'bolt://%s',
                getenv('NEO4J_HOST')
            );
        }
        return $boltUrl;
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
    protected function emptyDB()
    {
        $this->getSession()->run('MATCH (n) DETACH DELETE n');
        $this->dropIndexesAndConstraints();
    }

    protected function dropIndexesAndConstraints()
    {
        $indexRecords = $this->getSession()->run('CALL db.indexes()');
        $drops = array_map(function ($record){
            $name = $record->get('name');
            if (strpos($name, 'index') !== false){
                return "DROP INDEX $name";
            }
            return "DROP CONSTRAINT $name";
        }, $indexRecords->getRecords());
        foreach ($drops as $drop) {
            $this->getSession()->run($drop);
        }
    }
}
