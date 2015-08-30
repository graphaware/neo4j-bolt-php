<?php

namespace GraphAware\Bolt;

use GraphAware\Bolt\IO\Socket;
use GraphAware\Bolt\Protocol\SessionRegistry;
use Neo4j\PackStream\PackStream\Packer;
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
        $this->io->write($msg);
        $rawHandshakeResponse = $this->io->read(4);
        $response = unpack('N', $rawHandshakeResponse);
        $version = $response[1];
        return $version;
    }

    public function recv()
    {
        $nextChunkLength = 2;
        do {
            echo '----' . PHP_EOL;
            $chunkHeader = $this->io->read($nextChunkLength);
            $chunkSize = $this->getChunkLength($chunkHeader);
            $this->io->read($chunkSize);
            $nextChunkLength = $this->getChunkLength($this->io->read(2));
            echo '-';
            var_dump($nextChunkLength);
            echo '-' . PHP_EOL;
        } while($nextChunkLength > 0);
    }

    public function getChunkLength($chunkHeader)
    {
        $length = hexdec(bin2hex($chunkHeader));
        if (0 === $length) {
            echo 'END MESSAGE ENCOUNTERED' . PHP_EOL;
        }
        return (int) $length;
    }
}