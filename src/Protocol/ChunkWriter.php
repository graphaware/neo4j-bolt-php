<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol;

use GraphAware\Bolt\IO\AbstractIO;
use GraphAware\Bolt\PackStream\Packer;
use Symfony\Component\Stopwatch\Stopwatch;

class ChunkWriter
{
    const MAX_CHUNK_SIZE = 8192;

    /**
     * @var \GraphAware\Bolt\IO\AbstractIO
     */
    protected $io;

    /**
     * @var \GraphAware\Bolt\PackStream\Packer
     */
    protected $packer;

    protected $stopwatch;

    /**
     * @param \GraphAware\Bolt\IO\AbstractIO $io
     */
    public function __construct(AbstractIO $io, Packer $packer)
    {
        $this->io = $io;
        $this->packer = $packer;
        $this->stopwatch = new Stopwatch();
    }

    /**
     * @param \GraphAware\Bolt\Protocol\Message\AbstractMessage[] $messages
     */
    public function writeMessages(array $messages)
    {
        $raw = '';
        foreach ($messages as $msg) {
            $chunkData = $msg->getSerialization();
            $chunks = $this->splitChunk($chunkData);
            foreach ($chunks as $chunk) {
                $raw .= $this->packer->getSizeMarker($chunk);
                $raw .= $chunk;
            }
            $raw .= $this->packer->getEndSignature();
        }
        $i = 'write' . uniqid();
        $this->stopwatch->start($i);
        $this->io->write($raw);
        $e = $this->stopwatch->stop($i);
        //echo 'Write duration : ' . $e->getDuration() . PHP_EOL;
    }

    public function splitChunk($data)
    {
        $chunks = str_split($data, self::MAX_CHUNK_SIZE);

        return $chunks;
    }
}