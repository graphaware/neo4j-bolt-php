<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt;

use GraphAware\Bolt\Exception\IOException;
use GraphAware\Bolt\IO\Socket;
use GraphAware\Bolt\Protocol\SessionRegistry;
use GraphAware\Bolt\PackStream\Packer;
use Symfony\Component\EventDispatcher\EventDispatcher;
use GraphAware\Bolt\Exception\HandshakeException;
use GraphAware\Bolt\Protocol\V1\Session as SessionV1;

class Driver
{
    const VERSION = '1.0.0-DEV';

    const VERSION_ID = '10000';

    const MAJOR_VERSION = '1';

    const MINOR_VERSION = '0';

    const RELEASE_VERSION = '0';

    const EXTRA_VERSION = 'DEV';

    protected $io;

    protected $dispatcher;

    protected $sessionRegistry;

    protected $versionAgreed = false;

    public static function getUserAgent()
    {
        return 'GraphAware-BoltPHP/' . self::VERSION;
    }

    public function __construct($host, $port)
    {
        $this->io = new Socket($host, $port);
        $this->dispatcher = new EventDispatcher();
        $this->sessionRegistry = new SessionRegistry($this->io, $this->dispatcher);
        $this->sessionRegistry->registerSession(SessionV1::class);
    }

    /**
     * @return \Graphaware\Bolt\Protocol\SessionInterface
     */
    public function getSession()
    {
        if (!$this->versionAgreed) {
            $this->versionAgreed = $this->handshake();
        }

        return $this->sessionRegistry->getSession($this->versionAgreed);
    }

    /**
     * @return mixed
     * @throws \GraphAware\Bolt\Exception\HandshakeException
     */
    public function handshake()
    {
        $packer = new Packer();
        if (!$this->io->isConnected()) {
            $this->io->reconnect();
        }
        $msg = '';
        foreach (array(1,0,0,0) as $v) {
            $msg .= $packer->packBigEndian($v);
        }
        try {
            $this->io->write($msg);
            $rawHandshakeResponse = $this->io->read(4);
            $response = unpack('N', $rawHandshakeResponse);
            $version = $response[1];

            if (0 === $version) {
                $this->throwHandshakeException(sprintf('Handshake Exception. Unable to negotiate a version to use. Proposed versions were %s',
                    json_encode(array(1,0,0,0))));
            }

            return $version;
        } catch (IOException $e) {
            $this->throwHandshakeException($e->getMessage());
        }
    }

    /**
     * @param string $message
     */
    private function throwHandshakeException($message)
    {
        throw new HandshakeException($message);
    }
}