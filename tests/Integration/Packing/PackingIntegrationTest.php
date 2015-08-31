<?php

namespace GraphAware\Bolt\Tests\Integration\Packing;

use GraphAware\Bolt\Tests\Integration\IntegrationTestCase;

class PackingIntegrationTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->emptyDB();
        $this->client->createIndex('Integer', 'value');
    }

    

    public function testInt8IntegersPacking()
    {
        $this->doRangeTest(128, 1000);
    }

    public function testInt16Packing()
    {
        $this->doRangeTest(32768, 34000);
    }

    private function doRangeTest($min, $max)
    {
        $range = range($min, $max);
        $tx = $this->client->prepareTransaction();
        foreach ($range as $i) {
            $q = 'CREATE (n:Integer) SET n.value = {value}';
            $tx->pushQuery($q, ['value' => $i]);
        }
        $tx->commit();

        $session = $this->driver->getSession();
        foreach ($range as $i) {
            $response = $session->run('MATCH (n:Integer) WHERE n.value = {value} RETURN n.value', ['value' => $i]);
            $this->assertCount(1, $response->getRecords());
        }
    }
}