<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$bolt = new Bolt('localhost', 7687);
$stopwatch = new Stopwatch();

$session = $bolt->getSession();
//$response = $session->run('MATCH (n:Person) WHERE HAS(n.name) RETURN n, n.name as name, n.born as birthyear LIMIT 10', array('years' => range(1940,1999)));

$stopwatch->start("run");
//$result = $session->run("CREATE (n:Node) RETURN n", array());
$r = $session->run("CREATE (n:Node) RETURN n", array());
//print_r($result);
//print_r($r);
$e = $stopwatch->stop('run');
echo $e->getDuration() . PHP_EOL;

$pipeline = $session->createPipeline();
for ($i = 1; $i < 1500; ++$i) {
    //$pipeline->push("MERGE (p:PipelineTest {id: {id}}) RETURN id(p) as pid", ['id' => $i]);
}
$pipeline->push('UNWIND {ids} as id MERGE (p:PipelineTest {id: id})', ['ids' => range(0,5000)]);
$stopwatch->start('pipeline');
$results = $pipeline->flush();
$e = $stopwatch->stop('pipeline');
echo count($results) . ' - - ' . $e->getDuration() . PHP_EOL;
