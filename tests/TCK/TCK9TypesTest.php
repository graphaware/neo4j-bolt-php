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

    private function runValue($value)
    {
        $driver = $this->getDriver();
        $session = $driver->getSession();
        $result = $session->run("RETURN {x} as x", ['x' => $value]);

        return $result->getRecord()['x'];
    }
}