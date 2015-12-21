<?php

namespace GraphAware\Bolt\Tests\Integration\UseCase;

use GraphAware\Bolt\Tests\Integration\IntegrationTestCase;

/**
 * @group integration
 * @group use-case
 * @group pipeline
 */
class PipelineUseCaseTest extends IntegrationTestCase
{
    public function testPipelineWithMultipleStatements()
    {
        $driver = $this->getDriver();
        $pipeline = $driver->session()->createPipeline();
        $pipeline->push("CREATE (n:PipelineTest)");
        $pipeline->push("CREATE (n:PipelineTest)");
        $results = $pipeline->flush();
        $this->assertCount(2, $results);
    }
}