<?php

namespace GraphAware\Bolt\Tests\Integration\Packing;

use GraphAware\Bolt\Tests\Integration\IntegrationTestCase;

/**
 * @group packstream
 * @group integration
 * @group graphstructure
 */
class PackingGraphStructureIntegrationTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->emptyDB();
    }

    /**
     * @group structure-node
     */
    public function testUnpackingNode()
    {
        $session = $this->getSession();
        $result = $session->run("CREATE (n:Node) SET n.time = {t}, n.desc = {d} RETURN n", ['t' => time(), 'd' => 'GraphAware is awesome !']);

        //$this->assertTrue($result->getRecord()['n'] instanceof Node);
        //$this->assertEquals('GraphAware is awesome !', $result->getRecord()['n']->getProperty('desc'));
    }

    public function testUnpackingUnboundRelationship()
    {
        $session = $this->getSession();
        $result = $session->run("CREATE (n:Node)-[r:RELATES_TO {since: 1992}]->(b:Node) RETURN r");
        $record = $result->getRecord();

        //$this->assertTrue($record['r'] instanceof Relationship);
        //$this->assertEquals(1992, $record['r']->getProperty('since'));
    }

    public function testUnpackingNodesCollection()
    {
        $session = $this->getSession();
        $session->run("FOREACH (x in range(1,3) | CREATE (n:Node {id: x}))");
        $result = $session->run("MATCH (n:Node) RETURN collect(n) as nodes");

        $this->assertCount(3, $result->getRecord()['nodes']);
        foreach ($result->getRecord()['nodes'] as $node) {
            //$this->assertTrue(in_array('Node', $node->getLabels()));
        }
    }

    /**
     * @group path
     */
    public function testUnpackingPaths()
    {
        $session = $this->getSession();
        $session->run("MATCH (n) DETACH DELETE n");
        $session->run("CREATE (a:A {k: 'v'})-[:KNOWS]->(b:B {k:'v2'})-[:LIKES]->(c:C {k:'v3'})<-[:KNOWS]-(a)");
        $result = $session->run("MATCH p=(a:A)-[r*]->(b) RETURN p, length(p) as l");
    }

    /**
     * @group single-elt
     */
    public function testSingleElt()
    {
        $session = $this->getSession();
        $result = $session->run("CREATE (n:TestNode), (b:TestNode) RETURN n as new, b as new2");

        //print_r($result);
    }
}
