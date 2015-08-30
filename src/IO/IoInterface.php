<?php

namespace GraphAware\Bolt\IO;

interface IoInterface
{
    public function write($data);

    public function read($n);

    public function connect();

    public function reconnect();

    public function isConnected();
}