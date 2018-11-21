<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Tests\IntegrationTestCase;

/**
 * Class TemporalValuesIntegrationTest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group temporal
 */
class TemporalValuesIntegrationTest extends IntegrationTestCase
{
    public function testDateValue()
    {
        $query = 'CREATE (n:Person {name:\'Fred\', birthday:date(\'2018-11-21\')}) RETURN n';
        $session = $this->driver->session();
        $result = $session->run($query);
        var_dump($result->firstRecord()->get('n'));
    }
}