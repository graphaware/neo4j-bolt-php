<?php

namespace PTS\Bolt\Tests\Integration;

use PTS\Bolt\Exception\PipelineFinishedException;
use PTS\Bolt\Exception\BoltInvalidArgumentException;
use PTS\Bolt\Tests\IntegrationTestCase;

class PipelineIntegrationTest extends IntegrationTestCase
{
     /**
     * @group pipeline
     */
    public function testPipelinesCanBeRun()
    {
        $session = $this->getSession();
        $pipeline = $session->createPipeline();
        $pipeline->push('CREATE (n:User {id:1}) RETURN n', [], 'first');
        $pipeline->push('CREATE (n:User {id:$id}) RETURN n', ['id' => 2]);
        $pipeline->push('CREATE (n:User {id:3}) RETURN n', [], 'last');
        $results = $pipeline->run();
        $this->assertEquals(3, $results->size());
        $this->assertEquals(3, $results->get('last')->firstRecord()->getByIndex(0)->get('id'));
        $results->next();
        $this->assertEquals(2, $results->current()->firstRecord()->getByIndex(0)->get('id'));

        // no more statements after pipeline has run
        $this->setExpectedException(PipelineFinishedException::class);
        $pipeline->push('CREATE (n:User {id:1}) RETURN n', [], 'first');
    }

    
    public function testPipelinesDoNotAllowEmptyStatements()
    {
        $session = $this->getSession();
        $pipeline = $session->createPipeline();
        // no empty statements
        $this->setExpectedException(BoltInvalidArgumentException::class);
        $pipeline->push('');
    }

    public function testPipelinesCanNotBeRunTwice()
    {
        $session = $this->getSession();
        $pipeline = $session->createPipeline();
        $pipeline->push('CREATE (n:User {id:1}) RETURN n', [], 'first');
        $pipeline->run();
        $this->setExpectedException(PipelineFinishedException::class);
        $pipeline->run();
    }
}
