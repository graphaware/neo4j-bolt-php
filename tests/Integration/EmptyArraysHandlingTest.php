<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Common\Collections;

/**
 * Class EmptyArraysHandlingTest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group empty-list
 */
class EmptyArraysHandlingTest extends IntegrationTestCase
{
    public function testEmptyArrayAsListIsHandled()
    {
        $this->emptyDB();
        $query = 'MERGE (n:User {id: {id} }) 
        WITH n
        UNWIND {friends} AS friend
        MERGE (f:User {id: friend.name})
        MERGE (f)-[:KNOWS]->(n)';

        $params = ['id' => 'me', 'friends' => Collections::asList([])];
        $this->getSession()->run($query, $params);

        $result = $this->getSession()->run('MATCH (n:User) RETURN count(n) AS c');
        $this->assertEquals(1, $result->firstRecord()->get('c'));
    }

    public function testEmptyArrayAsMapIsHandled()
    {
        $this->emptyDB();
        $query = 'MERGE (n:User {id: {id} }) 
        WITH n
        UNWIND {friends}.users AS friend
        MERGE (f:User {id: friend.name})
        MERGE (f)-[:KNOWS]->(n)';

        $params = ['id' => 'me', 'friends' => Collections::asMap([])];
        $this->getSession()->run($query, $params);

        $result = $this->getSession()->run('MATCH (n:User) RETURN count(n) AS c');
        $this->assertEquals(1, $result->firstRecord()->get('c'));
    }
}