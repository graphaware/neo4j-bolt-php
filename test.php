<?php

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Stopwatch\Stopwatch;


$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost", \GraphAware\Bolt\Configuration::withCredentials('neo4j', 'password'));
$session = $driver->session();
$i = 0;
$stopwatch = new Stopwatch();
$stopwatch->start("e");
$result = $session->run("CREATE (n:User)-[r:KNOWS]->(x) RETURN n, r, x");
$e = $stopwatch->stop("e");

print_r($result);