<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\IO;

use Symfony\Component\EventDispatcher\EventDispatcher;

class StreamSocket extends AbstractIO
{
    protected $protocol;

    protected $host;

    protected $port;

    protected $context;

    protected $keepAlive;

    protected $eventDispatcher;

    private $sock;

    public function __construct($host, $port, $context = null, $keepAlive = false, EventDispatcher $eventDispatcher = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->context = $context;
        $this->keepAlive = $keepAlive;
        $this->eventDispatcher = $eventDispatcher;
        $this->protocol = 'tcp';
        if (is_null($this->context)) {
            $this->context = stream_context_create();
        } else {
            $this->protocol = 'ssl';
        }
    }

    public function write($data)
    {
        // TODO: Implement write() method.
    }

    public function read($n)
    {
        // TODO: Implement read() method.
    }

    public function connect()
    {
        // TODO: Implement connect() method.
    }

    public function reconnect()
    {
        // TODO: Implement reconnect() method.
    }

    public function isConnected()
    {
        // TODO: Implement isConnected() method.
    }

}