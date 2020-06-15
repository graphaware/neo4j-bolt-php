<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\Protocol\SessionInterface;
use GraphAware\Bolt\Tests\IntegrationTestCase;

/**
 * Class HandshakeIntegrationTest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group integration
 * @group handshake
 */
class HandshakeIntegrationTest extends IntegrationTestCase
{
    public function testHandshakeAgreeVersion()
    {
        $version = getenv('BOLT_VERSION') ? getenv('BOLT_VERSION') : 4;
        $session = $this->getSession();
        $this->assertInstanceOf(SessionInterface::class, $session);
        $this->assertEquals((int)$version, $session::getProtocolVersion());
    }

    public function testErrorIsThrownWhenNoVersionCanBeAgreed()
    {
        // needs some refactoring for mocking the session registry
    }
}
