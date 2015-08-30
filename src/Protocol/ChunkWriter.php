<?php

namespace GraphAware\Bolt\Protocol;

use GraphAware\Bolt\IO\AbstractIO;
use GraphAware\Bolt\Misc\Helper;
use GraphAware\Bolt\PackStream\Packer;
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

    /**
     * @param \GraphAware\Bolt\IO\AbstractIO $io
     */
    public function __construct(AbstractIO $io, Packer $packer)
    {
        $this->io = $io;
        $this->packer = $packer;
    }

    /**
     * @param \GraphAware\Bolt\Protocol\Message\AbstractMessage[] $messages
     */
    public function writeMessages(array $messages)
    {
        $raw = '';
        foreach ($messages as $msg) {
            $chunkData = $msg->getSerialization();
            $raw .= $this->packer->getSizeMarker($chunkData);
            $raw .= $chunkData;
            $raw .= $this->packer->getEndSignature();
        }
        $this->io->write($raw);
    }
}