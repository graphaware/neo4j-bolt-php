<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Bolt\Result\Type\Node;
use GraphAware\Bolt\Tests\IntegrationTestCase;
use GraphAware\Common\Cypher\Statement;
use GraphAware\Common\Transaction\TransactionState;

/**
 * Class TransactionIntegrationTest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group tx-it
 */
class TransactionIntegrationTest extends IntegrationTestCase
{
    public function testRunMultiple()
    {
        $this->emptyDB();

        $statements = array();

        for ($i = 0; $i < 5; ++$i) {
            $statements[] = Statement::create('CREATE (n:Test)');
        }

        $tx = $this->getSession()->transaction();
        $tx->begin();
        $tx->runMultiple($statements);
        $tx->commit();
        $this->assertXNodesWithTestLabelExist(5);
    }

    public function testRunSingle()
    {
        $this->emptyDB();

        $tx = $this->getSession()->transaction();
        $result = $tx->run(Statement::create('CREATE (n) RETURN id(n) as id'));
        $this->assertTrue($result->firstRecord()->hasValue('id'));
    }

    public function testManualRollbackOnException()
    {
        $this->emptyDB();

        $session = $this->getSession();
        $tx = $session->transaction();
        try {
            $tx->run(Statement::create("BLA BLA BLA"));
        } catch (MessageFailureException $e) {
            //
        }
        $result = $session->run('CREATE (n) RETURN n');
        $this->assertTrue($result->firstRecord()->get('n') instanceof Node);
        $this->assertEquals(TransactionState::ROLLED_BACK, $tx->status());
    }

    /**
     * @group tx-tag-multiple-fix
     */
    public function testRunMultipleInTransactionWithTags()
    {
        $this->emptyDB();

        $statements = array();
        for ($i = 0; $i < 5; ++$i) {
            $statements[] = Statement::create('CREATE (n:Test) RETURN n', [], sprintf('statement_%d', $i));
        }

        $tx = $this->getSession()->transaction();
        $results = $tx->runMultiple($statements);
        $this->assertEquals('statement_0', $results->results()[0]->statement()->getTag());
    }

    private function assertXNodesWithTestLabelExist($number = 1)
    {
        $result = $this->getSession()->run("MATCH (n:Test) RETURN count(n) as c");

        $this->assertEquals($number, $result->firstRecord()->get('c'));
    }
}
