<?php

namespace PTS\Bolt\Tests\Integration;

use PTS\Bolt\Protocol\SessionInterface;
use PTS\Bolt\Tests\IntegrationTestCase;
use PTS\Bolt\Driver;

/**
 * Class HandshakeIntegrationTest
 * @package PTS\Bolt\Tests\Integration
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
        $version = getenv('BOLT_VERSION') ? getenv('BOLT_VERSION') : 4;
        if ($version != 4) {
            $this->markTestSkipped('Version 1 is supported');
            return;
        }
        $driver = new Driver(
            $this->getBoltUrl(),
            $this->getConfig(),
            1
        );
        $this->setExpectedException(\PTS\Bolt\Exception\HandshakeException::class);
        $driver->session();
    }
}
