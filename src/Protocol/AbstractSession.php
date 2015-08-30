<?php

namespace GraphAware\Bolt\Protocol;

use GraphAware\Bolt\IO\AbstractIO;
use GraphAware\Bolt\PackStream\Serializer;
use GraphAware\Bolt\PackStream\Unpacker;
use GraphAware\Bolt\PackStream\Packer;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractSession implements SessionInterface
{
    protected $io;

    protected $dispatcher;

    protected $serializer;

    protected $packer;

    protected $unpacker;

    protected $writer;

    public function __construct(AbstractIO $io, EventDispatcherInterface $dispatcher)
    {
        $this->io = $io;
        $this->dispatcher = $dispatcher;
        $this->packer = new Packer();
        $this->unpacker = new Unpacker();
        $this->serializer = new Serializer($this->packer, $this->unpacker);
        $this->writer = new ChunkWriter($this->io, $this->packer);
    }
}