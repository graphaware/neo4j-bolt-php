<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt;

use PTS\Bolt\Exception\IOException;
use PTS\Bolt\IO\StreamSocket;
use PTS\Bolt\Protocol\SessionRegistry;
use PTS\Bolt\PackStream\Packer;
use PTS\Bolt\Protocol\V1\Session;
use PTS\Bolt\Protocol\V2\Session as SessionV2;
use PTS\Bolt\Protocol\V3\Session as SessionV3;
use PTS\Bolt\Protocol\V4\Session as SessionV4;
use GraphAware\Common\Driver\DriverInterface;
use phpDocumentor\Reflection\Types\Self_;
use Symfony\Component\EventDispatcher\EventDispatcher;
use PTS\Bolt\Exception\HandshakeException;

class Driver implements DriverInterface
{
    const VERSION = '4.0.0';

    const DEFAULT_TCP_PORT = 7687;

    const BOLT_VERSIONS = [4, 3, 2, 1];

    /**
     * @var StreamSocket
     */
    protected $io;

    /**
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * @var SessionRegistry
     */
    protected $sessionRegistry;

    /**
     * @var int
     */
    protected $versionAgreed = 0;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $credentials;

    /**
     * @var int
     */
    private $forceBoltVersion;

    /**
     * @return string
     */
    public static function getUserAgent()
    {
        return 'GraphAware-BoltPHP/'.self::VERSION;
    }

    /**
     * @param string $uri
     * @param Configuration|null $configuration
     * @param int $forceBoltVersion
     */
    public function __construct($uri, Configuration $configuration = null, $forceBoltVersion = 0)
    {
        $this->forceBoltVersion = $forceBoltVersion;
        $this->credentials = null !== $configuration ? $configuration->getValue('credentials', []) : [];
        $config = null !== $configuration ? $configuration : Configuration::create();
        $parsedUri = parse_url($uri);
        $host = isset($parsedUri['host']) ? $parsedUri['host'] : $parsedUri['path'];
        $port = isset($parsedUri['port']) ? $parsedUri['port'] : static::DEFAULT_TCP_PORT;
        $this->dispatcher = new EventDispatcher();
        $this->io = StreamSocket::withConfiguration($host, $port, $config, $this->dispatcher);
        $this->sessionRegistry = new SessionRegistry($this->io, $this->dispatcher);
        $this->sessionRegistry->registerSession(Session::class);
        $this->sessionRegistry->registerSession(SessionV2::class);
        $this->sessionRegistry->registerSession(SessionV3::class);
        $this->sessionRegistry->registerSession(SessionV4::class);
    }

    /**
     * @return Session
     */
    public function session()
    {
        if (null !== $this->session) {
            return $this->session;
        }

        if ($this->versionAgreed == 0) {
            $this->versionAgreed = $this->handshake();
        }

        $this->session = $this->sessionRegistry->getSession($this->versionAgreed, $this->credentials);

        return $this->session;
    }

    /**
     * @return int
     *
     * @throws HandshakeException
     */
    public function handshake()
    {
        $packer = new Packer();

        if (!$this->io->isConnected()) {
            $this->io->reconnect();
        }

        $msg = '';
        $msg .= chr(0x60).chr(0x60).chr(0xb0).chr(0x17);

        foreach ($this->getBoltVersions() as $v) {
            $msg .= $packer->packBigEndian($v, 4);
        }

        try {
            $this->io->write($msg);
            $rawHandshakeResponse = $this->io->read(4);
            $response = unpack('N', $rawHandshakeResponse);
            $version = $response[1];

            if (0 === $version) {
                $this->throwHandshakeException(sprintf(
                    'Handshake Exception. Unable to negotiate a version to use. Proposed versions were %s',
                    json_encode($this->getBoltVersions())
                ));
            }

            return $version;
        } catch (IOException $e) {
            $this->throwHandshakeException($e->getMessage());
        }
        return 0;
    }

    /**
     * @param string $message
     */
    private function throwHandshakeException($message)
    {
        throw new HandshakeException($message);
    }

    private function getBoltVersions()
    {
        if ($this->forceBoltVersion != 0) {
            return [$this->forceBoltVersion, 0, 0, 0];
        }
        return self::BOLT_VERSIONS;
    }

    /**
     * @return int
     */
    public function getAgreedVersion(): int
    {
        return $this->versionAgreed;
    }
}
