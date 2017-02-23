<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt;

use GraphAware\Bolt\Exception\IOException;
use GraphAware\Bolt\IO\StreamSocket;
use GraphAware\Bolt\Protocol\SessionRegistry;
use GraphAware\Bolt\PackStream\Packer;
use GraphAware\Bolt\Protocol\V1\Session;
use GraphAware\Common\Driver\DriverInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use GraphAware\Bolt\Exception\HandshakeException;

class Driver implements DriverInterface
{
    const VERSION = '1.5.4';

    const DEFAULT_TCP_PORT = 7687;

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
     * @var bool
     */
    protected $versionAgreed = false;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var array
     */
    protected $credentials;

    /**
     * @return string
     */
    public static function getUserAgent()
    {
        return 'GraphAware-BoltPHP/'.self::VERSION;
    }

    /**
     * @param string             $uri
     * @param Configuration|null $configuration
     */
    public function __construct($uri, Configuration $configuration = null)
    {
        $this->credentials = null !== $configuration ? $configuration->getValue('credentials', []) : [];
        /*
        $ctx = stream_context_create(array());
        define('CERTS_PATH',
        '/Users/ikwattro/dev/_graphs/3.0-M02-NIGHTLY/conf');
        $ssl_options = array(
            'cafile' => CERTS_PATH . '/cacert.pem',
            'local_cert' => CERTS_PATH . '/ssl/snakeoil.pem',
            'peer_name' => 'example.com',
            'allow_self_signed' => true,
            'verify_peer' => true,
            'capture_peer_cert' => true,
            'capture_peer_cert_chain' => true,
            'disable_compression' => true,
            'SNI_enabled' => true,
            'verify_depth' => 1
        );
        foreach ($ssl_options as $k => $v) {
            stream_context_set_option($ctx, 'ssl', $k, $v);
        }
        */

        $config = null !== $configuration ? $configuration : Configuration::create();
        $parsedUri = parse_url($uri);
        $host = isset($parsedUri['host']) ? $parsedUri['host'] : $parsedUri['path'];
        $port = isset($parsedUri['port']) ? $parsedUri['port'] : static::DEFAULT_TCP_PORT;
        $this->dispatcher = new EventDispatcher();
        $this->io = StreamSocket::withConfiguration($host, $port, $config, $this->dispatcher);
        $this->sessionRegistry = new SessionRegistry($this->io, $this->dispatcher);
        $this->sessionRegistry->registerSession(Session::class);
    }

    /**
     * @return Session
     */
    public function session()
    {
        if (null !== $this->session) {
            return $this->session;
        }

        if (!$this->versionAgreed) {
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

        foreach (array(1, 0, 0, 0) as $v) {
            $msg .= $packer->packBigEndian($v, 4);
        }

        try {
            $this->io->write($msg);
            $rawHandshakeResponse = $this->io->read(4);
            $response = unpack('N', $rawHandshakeResponse);
            $version = $response[1];

            if (0 === $version) {
                $this->throwHandshakeException(sprintf('Handshake Exception. Unable to negotiate a version to use. Proposed versions were %s',
                    json_encode(array(1, 0, 0, 0))));
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
