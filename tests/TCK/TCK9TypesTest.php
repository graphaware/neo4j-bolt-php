<?php

namespace GraphAware\Bolt\Tests\TCK;

/**
 * @group tck
 * @group tck9
 */
class TCK9TypesTest extends TCKTestCase
{
    /**
     * Scenario: To ensure safe escaped provision of user supplied values
     *  Given a session
     *  And a user supplied value of type<type>
     *  |null|
     *  |boolean|
     *  |int|
     *  |float|
     *  |string|
     *  |list<any>|
     *  |map<string:any>|
     *
     *  When I pass the value as parameter "x" to a "RETURN {x}" statement
     *  Then I should receive the same value in the result
     *
     */
    public function testSafeEscapedProvision()
    {
        // null
        $this->assertEquals(null, $this->runValue(null));

        // boolean
        $this->assertEquals(true, $this->runValue(true));
        $this->assertEquals(false, $this->runValue(false));

        // int
        $this->assertEquals(1, $this->runValue(1));
        $this->assertEquals(10000, $this->runValue(10000));
        $this->assertEquals(1000000000, $this->runValue(1000000000));

        // float
        $this->assertEquals(1.0, $this->runValue(1.0));
        $this->assertEquals(pi(), $this->runValue(pi()));

        // string
        $this->assertEquals('GraphAware is awesome !', $this->runValue('GraphAware is awesome !'));

        // list
        $this->assertEquals(array(0,1,2), $this->runValue(array(0,1,2)));
        $this->assertEquals(array("one", "two", "three"), $this->runValue(array("one", "two", "three")));

        // map
        $this->assertEquals(['zone' => 1, 'code' => 'neo.TransientError'], $this->runValue(['zone' => 1, 'code' => 'neo.TransientError']));
    }

    /**
     * Scenario: To handle a value of any type returned within a Cypher result
     *  Given a Result containing a value of type <type>
     *   |null|
     *   |boolean|
     *   |string|
     *   |float|
     *   |int|
     *   |list<any>|
     *   |map<string:any>|
     *   |node|
     *   |relationship|
     *   |path|
     *  When I extract the value from the Result
     *  Then it should be mapped to appropriate language-idiomatic value
     */
    public function testResultTypes()
    {
        $driver = $this->getDriver();
        $session = $driver->session();

        // null
        $result = $session->run("CREATE (n) RETURN n.key as nilKey");
        $this->assertEquals(null, $result->getRecord()->value('nilKey'));

        // boolean
        $result = $session->run("CREATE (n) RETURN id(n) = id(n) as bool, id(n) = 'a' as bool2");
        $this->assertEquals(true, $result->getRecord()->value('bool'));
        $this->assertEquals(false, $result->getRecord()->value('bool2'));

        // string
        $result = $session->run("CREATE (n {k: {value}}) RETURN n.k as v", ['value' => 'text']);
        $this->assertEquals('text', $result->getRecord()->value('v'));

        // float
        $result = $session->run("CREATE (n {k: {value}}) RETURN n.k as v", ['value' => 1.38]);
        $this->assertEquals(1.38, $result->getRecord()->value('v'));

        // int
        $result = $session->run("CREATE (n) RETURN id(n) as id");
        $this->assertInternalType('int', $result->getRecord()->value('id'));
        $this->assertTrue($result->getRecord()->value('id') >= 0);

        // list<any>
        $result = $session->run("CREATE (n:Person:Male) RETURN labels(n) as l");
        $this->assertInternalType('array', $result->getRecord()->value('l'));
        $this->assertTrue(array_key_exists(0, $result->getRecord()->value('l')));
        $this->assertTrue(array_key_exists(1, $result->getRecord()->value('l')));

        // map<string:any>
        $result = $session->run("CREATE (n:Node) RETURN {id: id(n), labels: labels(n)} as map");
        $this->assertInternalType('array', $result->getRecord()->value('map'));
        $this->assertTrue(array_key_exists('id', $result->getRecord()->value('map')));
        $this->assertTrue(array_key_exists('labels', $result->getRecord()->value('map')));
        $this->assertInternalType('int', $result->getRecord()->value('map')['id']);
        $this->assertInternalType('array', $result->getRecord()->value('map')['labels']);
        
    }

    private function runValue($value)
    {
        $driver = $this->getDriver();
        $session = $driver->session();
        $result = $session->run("RETURN {x} as x", ['x' => $value]);

        return $result->getRecord()->value('x');
    }
}