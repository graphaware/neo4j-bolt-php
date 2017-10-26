<?php

namespace GraphAware\Bolt\Tests\Result;

/**
 * Class MapAccessUnitTest
 * @package GraphAware\Bolt\Tests\Result
 *
 * @group result-unit
 */
class MapAccessUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultValueCanBePassed()
    {
        $map = new DummyMA(array('key1' => 'value1'));
        $this->assertEquals('value1', $map->value('key1'));
        $this->assertEquals('value2', $map->value('not_exist', 'value2'));
    }

    public function testExceptionIsThrownIfNotDefaultGiven()
    {
        $map = new DummyMA(array('key' => 'val'));
        $this->setExpectedException(\InvalidArgumentException::class);
        $map->value('not_exist');
    }
}
