<?php
/*
 * This file is part of the Neo4j PackStream package.
 *
 * (c) Christophe Willemsen <willemsen.christophe@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Neo4j\PackStream\Writer;

use GraphAware\Bolt\PackStream\Unpacker;
use Neo4j\PackStream\PackStream\Packer;
use Neo4j\PackStream\IO\Socket;
use Neo4j\PackStream\Version\Handshaker;

class Writer
{
    private $packer;

    private $io;

    private $handshaker;

    private $messages = [];

    protected $unpacker;

    public function __construct(Socket $socket)
    {
        $this->packer = new Packer();
        $this->io = $socket;
        $this->handshaker = new Handshaker($this->io, $this->packer);
        $this->unpacker = new Unpacker();
    }

    public function doHandShake()
    {
        return $this->handshaker->handshake();
    }

    public function writeQuery($query, array $params = array())
    {
        $queryChunk = $this->packer->getStandardQueryStructureMarker();
        $queryChunk .= $this->packer->getRunSignature();
        $queryChunk .= $this->packer->pack($query);
        $queryChunk .= $this->packer->pack($params);
        $this->messages[] = $this->packer->getSizeMarker($queryChunk) . $queryChunk . $this->packer->getEndSignature();
        $pullChunk = $this->packer->getPullAllMessage();
        $this->messages[] = $pullChunk;
    }

    public function flush()
    {
        $messagesCount = count($this->messages);
        if (!$this->io->isConnected()) {
            $this->doHandShake();
        }
        $stream = '';
        foreach ($this->messages as $message) {
            var_dump(bin2hex($message));
            $stream .= $message;
        }
        $this->io->write($stream);
        $records = [];
        $hasMore = true;
        $iteratiokns = 1;
        while ($iteratiokns <= $messagesCount) {
            echo 'iteration ' . $iteratiokns . ' / ' . $messagesCount . PHP_EOL;
            $str = $this->getNextStructure();
            if (!$str->isSuccess()) {
                //++$iteratiokns;
                throw new \RuntimeException('No success ');
            }
            var_dump($str);
            $endSuccess = false;
            while (!$endSuccess) {
                $str = $this->getNextStructure();
                if ($str->isSuccess()) {
                    $endSuccess = true;
                } else {
                    $records[] = $str;
                }
            }
            ++$iteratiokns;
        }

        $this->io->close();
        $this->messages = [];
    }

    /**
     * @return \GraphAware\Bolt\PackStream\Structure\StructureInterface
     */
    public function getNextStructure()
    {
        echo 'READING SOCKET ' . PHP_EOL;
        $message = $this->handshaker->recv();

        var_dump($message);

        return $this->unpacker->unpackMessage($message);

    }

    public function readMessage($msg)
    {
        $position = 0;
        echo 'processing read msg' . "\n";
        echo 'message hex is " ' . bin2hex($msg) . '"' . "\n";
        $byte = mb_substr($msg, 0, 1);
        $position++;

        // If Marker is Tiny Structure
        if ($this->isMarker($byte, 0xb0)) {
            // Next byte is the signature
            $sig_byte = mb_substr($msg, $position, 1);
            $position++;
            if ($this->isSignature($sig_byte, 0x70)) {
                echo 'Successed Signature found ' . "\n";

                return true;
            } elseif ($this->isSignature($sig_byte, 0x71)) {
                echo 'Record signature found ' . "\n";

                // If record is list
                $recordTypeByte = mb_substr($msg, $position, 1);
                $position++;
                if ($this->isMarker($recordTypeByte, 0x90)) {
                    echo 'Tiny list record ' . "\n";
                    $size = $this->getTinyListSize($recordTypeByte);
                    echo 'List size is : ' . $size . "\n";
                    $x = 1;
                    while($x <= $size) {
                        $marker = mb_substr($msg, $position, 1);
                        $position++;
                        if ($this->isUnpackedMarker($marker) || $this->isTinyInt($marker)) {
                            $value = $marker;
                            echo 'Value is : ' . hexdec(unpack('H*', $marker)[1]) . "\n";
                        }
                        $x++;

                    }

                }

                return false;
            }
        }
    }

    public function isMarker($byte, $nibble)
    {
        $marker_raw = hexdec(unpack('H*', $byte)[1]);
        $marker = $marker_raw & 0xF0;

        return $marker === $nibble;
    }

    public function isSignature($byte, $sig)
    {
        $raw = hexdec(unpack('H*', $byte)[1]);

        return $sig === $raw;
    }

    public function getTinyListSize($marker)
    {
        $m = hexdec(unpack('H*', $marker)[1]);

        return $m & 0x0f;
    }

    public function isUnpackedMarker($marker)
    {
        $raw = hexdec(unpack('H*', $marker)[1]);
        $unpacked = [0xc2, 0xc0, 0xc3];
        if (in_array($raw, $unpacked)) {
            return true;
        }
    }

    public function isTinyInt($byte)
    {
        $raw = hexdec(unpack('H*', $byte)[1]);
        $tinyInts = range(-16, 127);
        if (in_array($raw, $tinyInts)) {
            echo 'It is a TINY INT' . "\n";
            return true;
        }

        return false;
    }
}