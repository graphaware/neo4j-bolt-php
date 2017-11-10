<?php

namespace GraphAware\Bolt\Tests\Example;

use GraphAware\Bolt\Tests\IntegrationTestCase;
use GraphAware\Bolt\Result\Type\Node;

/**
 * @group example
 * @group movies
 */
class MovieExampleTest extends IntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->emptyDB();
        $q = <<<QUERY
CREATE (matrix1:Movie { title : 'The Matrix', year : '1999-03-31' })
CREATE (matrix2:Movie { title : 'The Matrix Reloaded', year : '2003-05-07' })
CREATE (matrix3:Movie { title : 'The Matrix Revolutions', year : '2003-10-27' })
CREATE (keanu:Actor { name:'Keanu Reeves' })
CREATE (laurence:Actor { name:'Laurence Fishburne' })
CREATE (carrieanne:Actor { name:'Carrie-Anne Moss' })
CREATE (keanu)-[:ACTS_IN { role : 'Neo' }]->(matrix1)
CREATE (keanu)-[:ACTS_IN { role : 'Neo' }]->(matrix2)
CREATE (keanu)-[:ACTS_IN { role : 'Neo' }]->(matrix3)
CREATE (laurence)-[:ACTS_IN { role : 'Morpheus' }]->(matrix1)
CREATE (laurence)-[:ACTS_IN { role : 'Morpheus' }]->(matrix2)
CREATE (laurence)-[:ACTS_IN { role : 'Morpheus' }]->(matrix3)
CREATE (carrieanne)-[:ACTS_IN { role : 'Trinity' }]->(matrix1)
CREATE (carrieanne)-[:ACTS_IN { role : 'Trinity' }]->(matrix2)
CREATE (carrieanne)-[:ACTS_IN { role : 'Trinity' }]->(matrix3)
QUERY;

        $this->getSession()->run($q);
    }

    public function testGetSimpleNode()
    {
        $q = 'MATCH (m:Movie {title: {title}}) RETURN m;';
        $p = ['title' => 'The Matrix'];

        $result = $this->getSession()->run($q, $p);

        $this->assertCount(1, $result->getRecords());
        $this->assertTrue(in_array('Movie', $result->firstRecord()->value('m')->labels()));
    }

    /**
     * Fix issue #2
     *
     * @link https://github.com/graphaware/neo4j-bolt-php/issues/2
     */
    public function testRecordViewThrowsExceptionWhenKeyDoesntExist()
    {
        $query = 'MATCH (m:Movie {title: {title} }) RETURN m';
        $result = $this->getSession()->run($query, ['title' => 'The Matrix']);
        $record = $result->firstRecord();

        $movieNode = $record->nodeValue('m');
        $this->assertInstanceOf(Node::class, $movieNode);

        $this->setExpectedException(\InvalidArgumentException::class);
        $record->nodeValue('z');
    }
}
