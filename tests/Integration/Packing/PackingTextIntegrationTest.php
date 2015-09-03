<?php

namespace GraphAware\Bolt\Tests\Integration\Packing;

use GraphAware\Bolt\Tests\Integration\IntegrationTestCase;

/**
 * Class PackingTextIntegrationTest
 * @package GraphAware\Bolt\Tests\Integration\Packing
 *
 * @group integration
 * @group packing
 * @group text
 */
class PackingTextIntegrationTest extends IntegrationTestCase
{
    /**
     * @var \GraphAware\Bolt\Protocol\SessionInterface
     */
    protected $session;

    public function setUp()
    {
        parent::setUp();
        $this->emptyDB();
        $this->client->createIndex('Text', 'value');
        $this->session = $this->driver->getSession();
    }

    public function testTinyTextPacking()
    {
        $this->doRangeTest(1, 15);
    }

    /**
     * @group text8
     */
    public function testText8Packing()
    {
        $this->doRangeTest(16, 255);
    }

    /**
     * @group text16
     */
    public function testText16Packing()
    {
        $this->doRangeTest(256,356);
        $this->doRangeTest(16351, 16383);
        //$this->doRangeTest(65500, 65535);
    }

    /**
     * @group text32
     * @group fail
     */
    public function testText32Packing()
    {
        $this->doRangeTest(65536, 65550);
        $this->doRangeTest(500000, 500000);
    }

    public function doRangeTest($min, $max)
    {
        foreach (range($min, $max) as $i) {
            $txt = str_repeat('a', $i);
            $q = 'CREATE (n:Text) SET n.value = {value}';
            $this->session->run($q, array('value' => $txt));
        }

        foreach (range($min, $max) as $i) {
            $txt = str_repeat('a', $i);
            $q = 'MATCH (n:Text) WHERE n.value = {value} RETURN count(n) as x';
            $response = $this->session->run($q, ['value' => $txt]);
            $this->assertCount(1, $response->getRecords());
            $this->assertEquals(1, $response->getRecords()[0]['x']);
            $q = 'MATCH (n:Text) WHERE n.value = {value} RETURN n.value';
            $response = $this->session->run($q, array('value' => $txt));
            $this->assertCount(1, $response->getRecords());
            $this->assertEquals($txt, $response->getRecords()[0]['n.value']);
        }
    }
}