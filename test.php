<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;

$bolt = new Bolt('localhost', 7687);

$session = $bolt->getSession();
$t = microtime(true);
$response = $session->run('MATCH (n:Person) WHERE HAS(n.name) RETURN n, n.name as name, n.born as birthyear LIMIT 10', array('years' => range(1940,1999)));
$e = microtime(true);
$d = $e - $t;
var_dump($d);
print_r($response);
exit();
$session->run('MATCH (n:Person) WHERE n.processed = {val} RETURN n', array('val' => true));
$session->run('MATCH (n:Person) SET n += {props}', array('props' => ['active' => false, 'admin' => 1]));
$session->run('MATCH (n:Person {name: {name}}) RETURN n', array('name' => 'roger', 'id' => 1));
//s$session->run('MATCH (n:Person {name: {name}}) RETURN n', array('id' => 1, 'name' => 'roger'));
//print_r($response);