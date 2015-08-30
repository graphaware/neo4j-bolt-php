<?php

require_once(__DIR__.'/vendor/autoload.php');

use Neo4j\PackStream\Session;

$session = new Session('localhost', 7687);
$version = $session->testHandShake();

$start = microtime(true);
$x = 1;
while ($x <= 1) {
    echo 'Iteration ' . $x . "\n";
    //$session->runQuery('MATCH (n:Person) RETURN {ids: collect(id(n)), labels: labels(n)}, 270 as x');
    $session->runQuery('MATCH (n:Person) RETURN n LIMIT 2');
    $x++;
}
$end = microtime(true);
$diff = $end - $start;
echo 'Query sent in ' . $diff . ' seconds';