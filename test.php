<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$bolt = new Bolt('localhost', 7687);
$stopwatch = new Stopwatch();

$session = $bolt->getSession();
//$session->run("MATCH (n) DETACH DELETE n");

for ($i = 0; $i < 1000; ++$i) {
    $session->run("FOREACH (x in range(1,1000) | CREATE (:Node {value:x}))");
}
$stopwatch->start('i');
for ($i = 0; $i < 1000; ++$i) {
    $session->run("MATCH (n:Node) WHERE NOT n:SecondLabel WITH n LIMIT 1000 SET n:SecondLabel");
}
$e = $stopwatch->stop('i');
echo $e->getDuration() . PHP_EOL;