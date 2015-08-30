<?php
/*
 * This file is part of the Neo4j PackStream package.
 *
 * (c) Christophe Willemsen <willemsen.christophe@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol;

use GraphAware\Bolt\PackStream\Unpacker;
use GraphAware\Bolt\IO\Socket;
use Neo4j\PackStream\PackStream\Packer;

class Handshaker
{
    private $io;

    private $packer;

    private $vh;

    public function __construct(Socket $io, Packer $packer)
    {
        $this->io = $io;
        $this->packer = $packer;
    }

    public function handshake()
    {
        $msg = '';
        foreach (array(1,0,0,0) as $v) {
            $msg .= $this->packer->packBigEndian($v);
        }
        echo 'Writing message ' . bin2hex($msg) . PHP_EOL;
        $this->io->write($msg);
        $rawHandshakeResponse = $this->io->read(4);
        echo 'Received message ' . bin2hex($rawHandshakeResponse) . PHP_EOL;
        $response = unpack('N', $rawHandshakeResponse);
        $version = $response[1];
        echo 'Received version ' . $version . ' to be used' . PHP_EOL;

        $ua = 'ExampleDriver/1.0';
        $init = $this->packer->getMessages(Packer::SIGNATURE_INIT, array($ua));
        $message = $this->packer->getSizeMarker($init) . $init . $this->packer->getEndSignature();
        echo 'Sending INIT message : ' . bin2hex($message) . PHP_EOL;
        $this->io->write($message);

        $message = $this->recv();
        //var_dump($structure);
        return $version;
    }

    public function unpackStructureHeader($structure)
    {
        $sizeMarkerByte = mb_substr($structure, 0, 1);
        // If it is TINY STRUCTURE, the signature is packed as high order nibble
        if ($this->isMarker($sizeMarkerByte, Packer::STRUCTURE_TINY)) {
            $signatureByte = mb_substr($structure, 1, 1);
            if (Packer::SIGNATURE_SUCCESS === $this->getSignature($signatureByte)) {
                echo 'SUCCESS';
                return '';
            }
        }
    }

    public function isMarker($byte, $nibble)
    {
        $marker_raw = hexdec(bin2hex($byte));
        $marker = $marker_raw & 0xF0;

        return $marker === $nibble;
    }

    public function isSignature($byte, $sig)
    {
        $raw = hexdec(bin2hex($byte));

        return $sig === $raw;
    }

    public function getSignature($byte)
    {
        foreach([Packer::SIGNATURE_FAILURE, Packer::SIGNATURE_IGNORE, Packer::SIGNATURE_RECORD, Packer::SIGNATURE_SUCCESS] as $signature) {
            if ($this->isSignature($byte, $signature)) {
                return $signature;
            }
        }
    }

    /**
     * @return \GraphAware\Bolt\Binary\Message
     * @throws \Neo4j\PackStream\Exception\Neo4jPackStreamIOException
     */
    public function recv()
    {
        $nextChunkLength = 2;
        do {
            $chunkHeader = $this->io->read(2);
            $chunkSize = $this->getChunkLength($chunkHeader);
            if ($chunkSize) {
                //$message->append($this->io->read($chunkSize));
            }
            $nextChunkLength = $chunkSize;
        } while($nextChunkLength > 0);
    }

    public function getChunkLength($chunkHeader)
    {
        $length = hexdec(bin2hex($chunkHeader));
        if (0 === $length) {
            echo 'END MESSAGE ENCOUNTERED' . PHP_EOL;
        }
        return $length;
    }
}