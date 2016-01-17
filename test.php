<?php

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Stopwatch\Stopwatch;

$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();
$stopwatch = new Stopwatch();
$p = $session->createPipeline();
foreach (range(0, 100) as $i) {
    $p->push("MATCH (n:User {login: {login} })-[:FOLLOWS]->(f)-[:FOLLOWS]->(fof) RETURN count(*)", ['login' => 'ikwattro']);
}
$stopwatch->start("e");
$result = $p->run();
$e = $stopwatch->stop("e");
var_dump($e->getDuration());