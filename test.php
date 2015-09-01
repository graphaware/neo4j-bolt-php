<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;

$bolt = new Bolt('localhost', 7687);

$session = $bolt->getSession();
$t = microtime(true);
//$response = $session->run('MATCH (n:Person) WHERE n.born > {year} RETURN n LIMIT 1', array('year' => -256));
//print_r($response);
//exit();
$response = $session->run('MATCH (n:Person) WHERE HAS(n.name) RETURN n, n.name as name, n.born as birthyear LIMIT 10', array('years' => range(1940,1999)));
$e = microtime(true);
$d = $e - $t;
var_dump($d);
//print_r($response);
//exit();
//$session->run('MATCH (n:Person) WHERE n.processed = {val} RETURN n', array('val' => true));
//$session->run('MATCH (n:Person) SET n += {props}', array('props' => ['active' => false, 'admin' => 1]));
//$session->run('MATCH (n:Person {name: {name}}) RETURN n', array('name' => 'roger', 'id' => 1));
//$session->run('MATCH (n:Integer {value: {value}}) RETURN n', array('value' => 1));
$v = -1*abs(pow(2, 63));
$session->run('MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE r,n');
$response = $session->run('CREATE (n:Node) SET n.value = {value} RETURN n', array('value' => $v));
print_r($response);
//print_r($response);