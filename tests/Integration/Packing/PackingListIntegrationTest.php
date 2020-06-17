<?php

namespace PTS\Bolt\Tests\Integration\Packing;

use PTS\Bolt\Tests\IntegrationTestCase;

class PackingListIntegrationTest extends IntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->emptyDB();
    }

    public function testPackingList32()
    {
        $session = $this->getSession();

        $session->run('UNWIND range(1, 40000) AS i CREATE (n:TestList {id: i})');
        $result = $session->run('MATCH (n:TestList) RETURN collect(n.id) AS list');
        $this->assertCount(40000, $result->firstRecord()->get('list'));
    }

    public function testListTiny()
    {
        $this->doRangeTest(1, 15);
    }

    public function testList8()
    {
        $this->doRangeTest(16, 18);
        $this->doRangeTest(253, 255);
    }

    public function testList16()
    {
        $this->doRangeTest(1024, 1026);
    }

    public function testList16High()
    {
        $this->doRangeTest(65533, 65535);
    }

    private function doRangeTest($min, $max)
    {
        $query = 'CREATE (n:ListTest) SET n.list = $props RETURN n';
        $session = $this->getSession();

        for ($i = $min; $i < $max; ++$i) {
            $parameters = [];
            for ($j = 1; $j <= $i; $j++) {
                $parameters[] = 1;
            }
            $result = $session->run($query, ['props' => $parameters]);
            $node = $result->firstRecord()->value('n');
            $this->assertEquals($i, count($node->value('list')));
        }
    }
}
