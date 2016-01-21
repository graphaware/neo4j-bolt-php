<?php

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Stopwatch\Stopwatch;


$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();
$i = 0;
$stopwatch = new Stopwatch();
$stopwatch->start("e");
$result = $session->run("MATCH (n:Person {name: {name} }) RETURN n", ['name' => 'Chris']);
$e = $stopwatch->stop("e");
var_dump($e->getDuration());

$p = $session->createPipeline();
$p->push("MATCH (n:Person) RETURN n LIMIT 500");
$p->push("MATCH (n:Person) RETURN n LIMIT 500");
$stopwatch->start("p");
$results = $p->run();
$e = $stopwatch->stop("p");
var_dump($e->getDuration());