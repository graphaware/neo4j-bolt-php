<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();

$stopwatch = new Stopwatch();

$stopwatch->start('e');
$result = $session->run("UNWIND range(0, 1000) as i CREATE (x:Node) RETURN x");
$e = $stopwatch->stop('e');
print_r($result->summarize()->updateStatistics());
echo $e->getDuration();