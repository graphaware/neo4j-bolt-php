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

interface SessionInterface
{
    public static function getProtocolVersion();

    public function run($statement, array $parameters = array());

    public function runPipeline(Pipeline $pipeline);
}