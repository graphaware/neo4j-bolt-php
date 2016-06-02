<?php

namespace GraphAware\Bolt\Tests\Integration;

/**
 * Class RealLifeUseCasesITest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group real-life
 */
class RealLifeUseCasesITest extends IntegrationTestCase
{
    public function testNestedEmptyArrays()
    {
        $this->emptyDB();
        $batches = [];
        for ($i = 1; $i < 10; ++$i) {
            $batches[] = $this->createBatch($i);
        }

        $query = 'UNWIND {batches} as batch
        MERGE (p:Person {id: batch.id})
        SET p += batch.props
        WITH p, batch
        UNWIND batch.prev as prev
        MERGE (o:Person {id: prev})
        MERGE (p)-[:KNOWS]->(o)';

        $session = $this->driver->session();
        $session->run($query, ['batches' => $batches]);
    }

    private function createBatch($i)
    {
        $batch = [
            'id' => $i,
            'props' => [
                'login' => sprintf('login%d', $i)
            ],
            'prev' => $i > 0 ? range(1, 10) : []
        ];

        return $batch;
    }
}