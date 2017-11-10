<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Bolt\Tests\IntegrationTestCase;
use GraphAware\Common\Cypher\Statement;
use GraphAware\Common\Type\Node;

/**
 * Class ExceptionDispatchTest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group integration
 * @group exception-dispatch
 */
class ExceptionDispatchTest extends IntegrationTestCase
{
    public function testExceptionsAreThrown()
    {
        $session = $this->getSession();

        $this->setExpectedException(MessageFailureException::class);
        $session->run("CREATE (n:)");

        try {
            $session->run("CR");
        } catch (MessageFailureException $e) {
            $this->assertEquals('Neo.ClientError.Statement.SyntaxError', $e->getStatusCode());
        }
    }

    public function testNeo4jStatusCodeIsAvalailble()
    {
        try {
            $this->getSession()->run("CR");
        } catch (MessageFailureException $e) {
            $this->assertEquals('Neo.ClientError.Statement.SyntaxError', $e->getStatusCode());
        }
    }

    public function testMessageFailuresAreHandled()
    {
        $session = $this->getSession();

        try {
            $session->run('CR');
        } catch (MessageFailureException $e) {
            //
        }

        $result = $session->run('CREATE (n) RETURN n');
        $this->assertTrue($result->firstRecord()->get('n') instanceof Node);
    }

    public function testMessageFailuresInTransactionsAreHandled()
    {
        $session = $this->getSession();
        $tx = $session->transaction();

        try {
            $tx->run(Statement::create('CR'));
        } catch (MessageFailureException $e) {
            //
        }
        $result = $session->run('CREATE (n) RETURN n');
        $this->assertTrue($result->firstRecord()->get('n') instanceof Node);
    }

    public function testMessageFailuresAreHandledInSequence()
    {
        $session = $this->getSession();
        $this->createConstraint('User', 'id');
        $session->run('MATCH (n:User) DETACH DELETE n');
        $session->run('CREATE (n:User {id:1})');
        $this->setExpectedException(MessageFailureException::class);
        $session->run('CREATE (n:User {id:1})');
    }

    public function testMessageFailuresAreHandledInPipelines()
    {
        $session = $this->getSession();
        $session->run('CREATE CONSTRAINT ON (u:User) ASSERT u.id IS UNIQUE');
        $session->run('MATCH (n:User) DETACH DELETE n');
        $session->run('CREATE (n:User {id:1})');
        $pipeline = $session->createPipeline();
        $pipeline->push('CREATE (n:User {id:3})');
        $pipeline->push('CREATE (n:User {id:4})');
        $pipeline->push('CREATE (n:User {id:1})');
        $pipeline->push('CREATE (n:User {id:5})');
        $this->setExpectedException(MessageFailureException::class);
        $pipeline->run();
    }

    /**
     * @group issue-111
     */
    public function testPipelineWithConstraintCreation()
    {
        $session = $this->getSession();
        $session->run('MATCH (n) DETACH DELETE n');
        $session->run('CREATE (n:User {id:1})');
        $pipeline = $session->createPipeline();
        $pipeline->push('CREATE CONSTRAINT ON (u:User) ASSERT u.id IS UNIQUE');
        $pipeline->push('CREATE (n:User {id:1})');
        $this->setExpectedException(MessageFailureException::class);
        $pipeline->run();
    }

    /**
     * @group exception-pipeline
     */
    public function testPipelinesCanBeRunAfterFailure()
    {
        $session = $this->getSession();
        $this->createConstraint('User', 'id');
        $session->run('MATCH (n:User) DETACH DELETE n');
        $session->run('CREATE (n:User {id:1})');
        $pipeline = $session->createPipeline();
        $pipeline->push('CREATE (n:User {id:3})');
        $pipeline->push('CREATE (n:User {id:4})');
        $pipeline->push('CREATE (n:User {id:1})');
        $pipeline->push('CREATE (n:User {id:5})');
        try {
            $pipeline->run();
        } catch (MessageFailureException $e) {
            //
        }

        $pipeline = $session->createPipeline();
        $pipeline->push('MATCH (n) DETACH DELETE n');
        $pipeline->push('CREATE (n) RETURN n');
        $results = $pipeline->run();
        $this->assertEquals(2, $results->size());
    }

    public function testConstraintViolationInTransaction()
    {
        $session = $this->getSession();

        $session->run('MATCH (n) DETACH DELETE n');
        $this->createConstraint('User', 'id');
        $this->createConstraint('User', 'login');
        $session->run('MERGE (n:User {login: "ikwattro", id:1})');
        $session->run('MERGE (n:User {login: "jexp", id: 2})');

        $tx = $session->createPipeline();
        $tx->push('MERGE (n:User {id:3}) SET n.login = "bachmanm"');
        $tx->push('MERGE (n:User {id:2}) SET n.login = "ikwattro"');
        $tx->push('MERGE (n:User {id:4}) SET n.login = "ale"');

        try {
            $tx->run();
            // should fail
            $this->assertFalse(true);
        } catch (\Exception $e) {
            $this->assertTrue(true);
        }
    }

    private function createConstraint($label, $key)
    {
        try {
            $this->getSession()->run(sprintf('CREATE CONSTRAINT ON (n:`%s`) ASSERT n.`%s` IS UNIQUE', $label, $key));
        } catch (MessageFailureException $e) {
            if ($e->getStatusCode() === 'Neo.ClientError.Schema.IndexAlreadyExists') {
                $this->dropIndex($label, $key);
                $this->createConstraint($label, $key);
            }

            throw $e;
        }
    }

    private function dropIndex($label, $key)
    {
        $this->getSession()->run(sprintf('DROP INDEX ON :%s(%s)', $label, $key));
    }
}
