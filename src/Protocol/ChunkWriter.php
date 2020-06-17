<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol;

use PTS\Bolt\IO\AbstractIO;
use PTS\Bolt\PackStream\Packer;

class ChunkWriter
{
    const MAX_CHUNK_SIZE = 8192;

    /**
     * @var AbstractIO
     */
    protected $io;

    /**
     * @var Packer
     */
    protected $packer;

    /**
     * @param AbstractIO $io
     * @param Packer $packer
     */
    public function __construct(AbstractIO $io, Packer $packer)
    {
        $this->io = $io;
        $this->packer = $packer;
    }

    /**
     * @param \PTS\Bolt\Protocol\Message\AbstractMessage[] $messages
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

        $this->io->write($raw);
    }

    /**
     * @param string $data
     *
     * @return array
     */
    public function splitChunk($data)
    {
        return str_split($data, self::MAX_CHUNK_SIZE);
    }
}
