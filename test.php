<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$bolt = new Bolt('localhost', 7687);
$stopwatch = new Stopwatch();

$session = $bolt->getSession();
//$response = $session->run('MATCH (n:Person) WHERE n.born > {year} RETURN n LIMIT 1', array('year' => -256));
//print_r($response);
//exit();
$response = $session->run('MATCH (n:Person) WHERE HAS(n.name) RETURN n, n.name as name, n.born as birthyear LIMIT 10', array('years' => range(1940,1999)));

//print_r($response);
//exit();
//$session->run('MATCH (n:Person) WHERE n.processed = {val} RETURN n', array('val' => true));
//$session->run('MATCH (n:Person) SET n += {props}', array('props' => ['active' => false, 'admin' => 1]));
//$session->run('MATCH (n:Person {name: {name}}) RETURN n', array('name' => 'roger', 'id' => 1));
//$session->run('MATCH (n:Integer {value: {value}}) RETURN n', array('value' => 1));
//$v = -1*abs(pow(2, 63));
//$session->run('MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE r,n');
//$response = $session->run('MATCH (n:Text) RETURN n');
//print_r($response);
//print_r($response);


$q = 'UNWIND {ids} as id CREATE (n:TxNode) SET n.id = id RETURN n';
$p = range(1,5000);
$stopwatch->start('run');
$result = $session->run($q, ['ids' => $p]);
$e = $stopwatch->stop('run');
echo $e->getDuration() . PHP_EOL;