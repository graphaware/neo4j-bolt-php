<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();

$stopwatch = new Stopwatch();

$stopwatch->start("engine1");
$result = $session->run("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN collect(o) as reco");
$e = $stopwatch->stop("engine1");
echo $e->getDuration() . PHP_EOL;
echo count($result->getRecord()->value('reco')) . PHP_EOL;

echo $result->getRecord()->value('n')->labels();

//print_r($result->getRecord()->value('reco'));

$stopwatch->start("test");
$result = $session->run("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL]->(o) RETURN o");
$e = $stopwatch->stop("test");
echo $e->getDuration() . PHP_EOL;

$pipeline = $session->createPipeline();
$pipeline->push("MATCH (n) WHERE id(n) = 1 MATCH (n)-[:REL*1..999]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL*1..100]->(o) RETURN o as reco, id(o) as score");
//$pipeline->push("MATCH (n) WHERE id(n) = 18 MATCH (n)-[:REL]->(o) RETURN o");
$stopwatch->start("pipeline");
$results = $pipeline->run();
$e = $stopwatch->stop("pipeline");
echo $e->getDuration() . PHP_EOL;
echo count($results);

//print_r($results);