<?php

namespace GraphAware\Bolt\Tests\Example;

use GraphAware\Bolt\Tests\Integration\IntegrationTestCase;

/**
 * @group example
 */
class MovieExampleTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->emptyDBWithBolt();
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
        $session = $this->driver->session();
        $session->run($q);

    }

    public function testGetSimpleNode()
    {
        $q = 'MATCH (m:Movie {title: {title}}) RETURN m;';
        $p = ['title' => 'The Matrix'];
        $session = $this->driver->session();
        $result = $session->run($q, $p);
        $this->assertCount(1, $result->getRecords());
        foreach ($result->getRecords() as $record) {
            $this->assertTrue(in_array('Movie', $record->value('m')->labels()));
        }
    }
}