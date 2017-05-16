<?php

namespace GraphAware\Bolt\Tests\Integration\Packing;

use GraphAware\Bolt\Tests\Integration\IntegrationTestCase;

class PackingListIntegrationTest extends IntegrationTestCase
{
    /**
     * @var \GraphAware\Bolt\Protocol\SessionInterface
     */
    protected $session;

    public function setUp()
    {
        parent::setUp();
        $this->emptyDB();
        $this->session = $this->driver->session();
    }

    public function testPackingList32()
    {
        $this->session->run('UNWIND range(1, 40000) AS i CREATE (n:TestList {id: i})');
        $result = $this->session->run('MATCH (n:TestList) RETURN collect(n.id) AS list');
        $this->assertCount(40000, $result->firstRecord()->get('list'));
    }
}