<?php

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Stopwatch\Stopwatch;


$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();
$i = 0;
$stopwatch = new Stopwatch();

$stopwatch->start("e");
$v = str_repeat('x', 1630);
$result = $session->run("CREATE (n:Text {value: {value} }) RETURN n", ['value' => $v]);
print_r($result);

/*
$pipeline = $session->createPipeline();
$pipeline->push("MATCH (n:Person) RETURN n LIMIT 10");
$pipeline->push("MATCH (n:Person) RETURN n.name LIMIT 5");
$results = $pipeline->run();
*/

$e = $stopwatch->stop("e");
var_dump($e->getDuration());