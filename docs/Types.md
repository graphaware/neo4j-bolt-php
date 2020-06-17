### Empty Arrays

Due to lack of Collections types in php, there is no way to distinguish when an empty array
should be treated as equivalent Java List or Map types.

Therefore you can use a wrapper around arrays for type safety :

```php
use GraphAware\Common\Collections;

        $query = 'MERGE (n:User {id: {id} }) 
        WITH n
        UNWIND {friends} AS friend
        MERGE (f:User {id: friend.name})
        MERGE (f)-[:KNOWS]->(n)';

        $params = ['id' => 'me', 'friends' => Collections::asList([])];
        $this->getSession()->run($query, $params);
        
// Or

        $query = 'MERGE (n:User {id: {id} }) 
        WITH n
        UNWIND {friends}.users AS friend
        MERGE (f:User {id: friend.name})
        MERGE (f)-[:KNOWS]->(n)';

        $params = ['id' => 'me', 'friends' => Collections::asMap([])];
        $this->getSession()->run($query, $params);

```