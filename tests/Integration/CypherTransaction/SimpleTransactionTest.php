<?php

namespace GraphAware\Bolt\Tests\Integration\CypherTransaction;

use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Bolt\Tests\Integration\IntegrationTestCase;

/**
 * @group integration
 * @group cypher-tx
 */
class SimpleTransactionTest extends IntegrationTestCase
{
    public function testTransactionCreatedWithCypher()
    {
        $session = $this->driver->getSession();
        $tx = $session->run('BEGIN');
        $result = $session->run('CREATE (n:CypherTransactionTest) RETURN n');
        $summary = $result->summarize();
        $session->run('COMMIT');
        $this->assertEquals(1, $summary->updateStatistics()->nodesCreated());
    }
}