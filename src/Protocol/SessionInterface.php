<?php

namespace GraphAware\Bolt\Protocol;

interface SessionInterface
{
    public static function getProtocolVersion();

    public function run($statement, array $parameters = array());

    public function runPipeline(Pipeline $pipeline);
}