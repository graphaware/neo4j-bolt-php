<?php

namespace GraphAware\Bolt\Protocol;

use GraphAware\Bolt\IO\AbstractIO;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SessionRegistry
{
    protected $io;

    protected $dispatcher;

    protected $sessions = [];

    public function __construct(AbstractIO $io, EventDispatcherInterface $dispatcher)
    {
        $this->io = $io;
        $this->dispatcher = $dispatcher;
    }

    public function registerSession($sessionClass)
    {
        $v = (int) $sessionClass::getProtocolVersion();
        if (array_key_exists($v, $this->sessions)) {
            throw new \RuntimeException(sprintf('There is already a Session registered for supporting Version#%d', $v));
        }

        $this->sessions[$v] = $sessionClass;
    }

    public function getSupportedVersions()
    {
        return array_keys($this->sessions);
    }

    public function supportsVersion($version)
    {
        $v = (int) $version;

        return array_key_exists($v, $this->sessions);
    }

    public function getSession($version)
    {
        $v = (int) $version;

        if (!$this->supportsVersion($v)) {
            throw new \InvalidArgumentException(sprintf('No session registered supporting Version %d', $v));
        }
        $class = $this->sessions[$v];

        return new $class($this->io, $this->dispatcher);
    }
}