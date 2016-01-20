<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\PackStream;

use GraphAware\Bolt\IO\AbstractIO;
use GraphAware\Bolt\Misc\Helper;
use GraphAware\Bolt\Protocol\Message\RawMessage;

class BytesWalker
{
    const ENCODING = 'ASCII';

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @var string
     */
    protected $bytes;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var \GraphAware\Bolt\IO\AbstractIO
     */
    protected $io;

    /**
     * BytesWalker constructor.
     * @param \GraphAware\Bolt\IO\AbstractIO $io
     */
    public function __construct($io)
    {
        if ($io instanceof RawMessage) {
            $this->bytes = $io->getBytes();
        } else {
            $this->io = $io;
            $this->io->assumeNonBlocking();
        }
    }

    /**
     * @param int $n
     *
     * @return string
     */
    public function read($n)
    {
        //echo 'asked : ' . $n . PHP_EOL;
        $remaining = ($n - (strlen($this->bytes)) + $this->position);
        //echo 'pos' . $this->position  .PHP_EOL;
        //echo 'l ' . strlen($this->bytes) . PHP_EOL;
        //echo 'remaining : ' . $remaining . PHP_EOL;
        //echo "p1" . $this->position . PHP_EOL;
        //echo 'asked' . $n . PHP_EOL;
        while ($remaining > 0) {
            //echo strlen($this->bytes) . PHP_EOL;
            //echo 'waiting ' . PHP_EOL;
            $this->io->wait();
            $new = $this->io->readChunk();
            //echo strlen($new) . PHP_EOL;
            //echo Helper::prettyHex($this->bytes) . PHP_EOL;
            //echo 'new chunk of ' . mb_strlen($new, self::ENCODING) . PHP_EOL;
            //echo Helper::prettyHex($new);
            $this->bytes .= $new;
            //echo 'new length is' . strlen($this->bytes) . PHP_EOL;
            $remaining -= strlen($new);
        }

        $data = substr($this->bytes, $this->position, $n);
        //echo Helper::prettyHex($data) . PHP_EOL;
        $this->position += $n;

        return $data;
    }

    public function forward($n)
    {
        $n = (int) $n;
        if (($this->position + $n) > $this->getLength()) {
            throw new \OutOfBoundsException(sprintf('No more bytes to read'));
        }

        $this->position += $n;
    }

    public function setPosition($n)
    {
        $n = (int) $n;
        if ($n > $this->getLength()) {
            throw new \OutOfBoundsException(sprintf('Require position out of bound'));
        }

        $this->position = $n;
    }

    /**
     * @param int $n
     */
    public function rewind($n)
    {
        $n = (int) $n;
        if ($n > $this->position) {
            throw new \InvalidArgumentException(sprintf('You try to rewind %d characters, but current position is %d',
                $n,
                $this->position
            ));
        }

        $this->position -= $n;
    }

    public function getLength()
    {
        $this->length = strlen($this->bytes, $this->encoding);
    }

    public function getPosition()
    {
        return $this->position;
    }
}