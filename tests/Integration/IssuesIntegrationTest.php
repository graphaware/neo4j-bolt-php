<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Configuration;
use GraphAware\Bolt\GraphDatabase;
use GraphAware\Bolt\Result\Result;

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
        $session = $this->driver->session();
        $result = $session->run('CREATE (n:IssueNode) SET n = {props} RETURN n', ['props' => $props]);
        print_r($result);
    }

    /**
     * @group context-interface
     */
    public function testBindToInterface()
    {
        $config = Configuration::newInstance()
            ->bindToInterface('0:0');
        $driver = GraphDatabase::driver('bolt://localhost:7687', $config);
        $session = $driver->session();
        $result = $session->run('MATCH (n) RETURN count(n)');
        $this->assertInstanceOf(Result::class, $result);
    }
}