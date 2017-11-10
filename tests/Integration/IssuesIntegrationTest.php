<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Configuration;
use GraphAware\Bolt\GraphDatabase;
use GraphAware\Bolt\Result\Result;
use GraphAware\Bolt\Tests\IntegrationTestCase;

class IssuesIntegrationTest extends IntegrationTestCase
{
    /**
     * @group issue-9
     */
    public function testIssue9()
    {
        $this->emptyDB();
        // create node with 22 properties

        $props = [];
        for ($i = 0; $i < 22; ++$i) {
            $props['prop'.$i] = $i;
        }
        $this->assertCount(22, $props);
        $this->getSession()->run('CREATE (n:IssueNode) SET n = {props} RETURN n', ['props' => $props]);
    }

    /**
     * @group context-interface
     */
    public function testBindToInterface()
    {
        $config = Configuration::create()
            ->bindToInterface('0:0');
        $driver = GraphDatabase::driver('bolt://localhost:7687', $config);
        $result = $driver->session()->run('MATCH (n) RETURN count(n)');
        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @see https://github.com/graphaware/neo4j-php-client/issues/60
     * @group issue60
     */
    public function testIssue60()
    {
        $this->emptyDB();
        $timestamp = time() * 1000;

        $session = $this->getSession();
        $result = $session->run('CREATE (n:Node {time: {time} }) RETURN n.time as t', ['time' => $timestamp]);
        $this->assertEquals($timestamp, $result->firstRecord()->get('t'));

        $this->emptyDB();
        $time = 1475921198602;
        $session->run('CREATE (n:Node) SET n.time = {time}', ['time' => $time]);
        $result = $session->run('MATCH (n:Node) RETURN n.time as t');
        $this->assertEquals($time, $result->firstRecord()->get('t'));
    }
}
