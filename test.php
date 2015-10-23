<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$bolt = new Bolt('localhost', 7687);
$stopwatch = new Stopwatch();

$session = $bolt->getSession();
//$response = $session->run('MATCH (n:Person) WHERE HAS(n.name) RETURN n, n.name as name, n.born as birthyear LIMIT 10', array('years' => range(1940,1999)));


$q = 'MATCH (n) DETACH DELETE n';
$p = range(1,5000);
$stopwatch->start('run');
$result = $session->run($q, ['ids' => $p]);
$e = $stopwatch->stop('run');
echo $e->getDuration() . PHP_EOL;