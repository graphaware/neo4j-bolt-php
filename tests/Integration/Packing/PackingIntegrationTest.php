<?php

namespace GraphAware\Bolt\Tests\Integration\Packing;

use GraphAware\Bolt\Tests\Integration\IntegrationTestCase;

/**
 * Class PackingIntegrationTest
 * @package GraphAware\Bolt\Tests\Integration\Packing
 *
 * @group packstream
 * @group integration
 * @group integers
 */
class PackingIntegrationTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->emptyDB();
        $this->client->createIndex('Integer', 'value');
    }

    public function testTinyIntegersPacking()
    {
        $this->doRangeTest(0, 127);
    }

    public function testMinTinyIntegersPacking()
    {
        $this->doRangeTest(-16, -1);
    }

    public function testMinIntegers8Packing()
    {
        $this->doRangeTest(-128, -17);
    }

    public function testInt8IntegersPacking()
    {
        $this->doRangeTest(128, 1000);
    }

    public function testInt8IntegersPackingEnd()
    {
        $this->doRangeTest(32000, 32768);
    }

    public function testInt16Packing()
    {
        $this->doRangeTest(32768, 34000);
    }

    public function testMinIntegers16Packing()
    {
        $this->doRangeTest(-200, -129);
    }

    public function testMinIntegers16PackingEnd()
    {
        $this->doRangeTest(-32768, -32700);
    }

    public function testMinIntegers32Packing()
    {
        $this->doRangeTest(-33000, -32769);
    }

    public function testMinIntegers32End()
    {
        $min = (-1*abs(pow(2,31)));
        $this->doRangeTest($min, $min + 100);
    }

    /**
     * @group 64
     */
    public function testMinIntegers64()
    {
        $max = (-1*abs(pow(2,31)))-1;
        $this->doRangeTest($max - 100, $max);
    }

    /**
     * @group 64
     */
    public function testMin64IntegersEnd()
    {
        $this->markTestSkipped();
        $min = (-1*abs(pow(2,63)));
        var_dump((string) $min);
        $this->doRangeTest($min, $min+100);
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