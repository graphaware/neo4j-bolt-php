<?php

namespace GraphAware\Bolt\Tests\Integration;

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
}