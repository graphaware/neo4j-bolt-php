<?php

namespace PTS\Bolt\Tests;

use PTS\Bolt\Configuration;
use PTS\Bolt\Driver;
use PTS\Bolt\GraphDatabase;

class IntegrationTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PTS\Bolt\Driver
     */
    protected $driver;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        $version = getenv('BOLT_VERSION') ? getenv('BOLT_VERSION') : 0;
        $this->driver = new Driver(
            $this->getBoltUrl(),
            $this->getConfig(),
            (int)$version
        );
    }

    protected function getConfig()
    {
        return getenv('NEO4J_USER') ?
            Configuration::create()->withCredentials(getenv('NEO4J_USER'), getenv('NEO4J_PASSWORD'))
            : Configuration::create();
    }

    /**
     * @return string
     */
    protected function getBoltUrl()
    {
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
     * @return \PTS\Bolt\Driver
     */
    protected function getDriver()
    {
        return $this->driver;
    }

    /**
     * @return \PTS\Bolt\Protocol\SessionInterface
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
        $drops = array_map(function ($record) {
            $name = $record->get('name');
            // older neo4j version
            if (!$name) {
                if ($record->get('type') == 'node_label_property') {
                    return 'DROP ' . $record->get('description');
                }
                $label = $record->get('label');
                $property = $record->get('properties')[0];
                return "DROP CONSTRAINT ON (n:$label) ASSERT n.$property IS UNIQUE";
            }
            if (strpos($name, 'index') !== false) {
                return "DROP INDEX $name";
            }

            return "DROP CONSTRAINT $name";
        }, $indexRecords->getRecords());
        foreach ($drops as $drop) {
            $this->getSession()->run($drop);
        }
    }
}
