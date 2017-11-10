<?php

namespace GraphAware\Bolt\Tests\Integration\Packing;

use GraphAware\Bolt\Tests\IntegrationTestCase;

/**
 * @group packing
 * @group integration
 * @group floats
 */
class PackingFloatsIntegrationTest extends IntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->emptyDB();
    }

    public function testPackingFloatsPositive()
    {
        $session = $this->getSession();

        for ($x = 1; $x < 1000; ++$x) {
            $result = $session->run("CREATE (n:Float) SET n.prop = {x} RETURN n.prop as x", ['x' => $x/100]);
            $this->assertEquals($x/100, $result->getRecord()->value('x'));
        }
    }

    public function testPackingFloatsNegative()
    {
        $session = $this->getSession();

        for ($x = -1; $x > -1000; --$x) {
            $result = $session->run("CREATE (n:Float) SET n.prop = {x} RETURN n.prop as x", ['x' => $x/100]);
            $this->assertEquals($x/100, $result->getRecord()->value('x'));
        }
    }

    public function testPi()
    {
        $result = $this->getSession()->run("CREATE (n:Float) SET n.prop = {x} RETURN n.prop as x", ['x' => pi()]);
        $this->assertEquals(pi(), $result->getRecord()->value('x'));
    }
}
