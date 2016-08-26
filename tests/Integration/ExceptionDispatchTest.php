<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\GraphDatabase;
use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Common\Cypher\Statement;
use GraphAware\Common\Type\Node;

/**
 * Class ExceptionDispatchTest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group integration
 * @group exception-dispatch
 */
class ExceptionDispatchTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionsAreThrown()
    {
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();

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
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();

        try {
            $session->run("CR");
        } catch (MessageFailureException $e) {
            $this->assertEquals('Neo.ClientError.Statement.SyntaxError', $e->getStatusCode());
        }
    }

    public function testMessageFailuresAreHandled()
    {
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();
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
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();
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
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();
        $session->run('CREATE CONSTRAINT ON (u:User) ASSERT u.id IS UNIQUE');
        $session->run('MATCH (n:User) DETACH DELETE n');
        $session->run('CREATE (n:User {id:1})');
        $this->setExpectedException(MessageFailureException::class);
        $session->run('CREATE (n:User {id:1})');
    }

    public function testMessageFailuresAreHandledInPipelines()
    {
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();
        $session->run('CREATE CONSTRAINT ON (u:User) ASSERT u.id IS UNIQUE');
        $session->run('MATCH (n:User) DETACH DELETE n');
        $session->run('CREATE (n:User {id:1})');
        $pipeline = $session->createPipeline();
        $pipeline->push('CREATE (n:User {id:3})');
        $pipeline->push('CREATE (n:User {id:4})');
        $pipeline->push('CREATE (n:User {id:1})');
        $pipeline->push('CREATE (n:User {id:5})');
        $this->setExpectedException(MessageFailureException::class);
        $results = $pipeline->run();
    }

    /**
     * @group exception-pipeline
     */
    public function testPipelinesCanBeRunAfterFailure()
    {
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();
        $session->run('CREATE CONSTRAINT ON (u:User) ASSERT u.id IS UNIQUE');
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
}