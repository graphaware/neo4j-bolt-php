<?php

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Stopwatch\Stopwatch;


$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();
$i = 0;
$stopwatch = new Stopwatch();

$stopwatch->start("e");
$result = $session->run("MATCH (b:Person {name: {name} }) RETURN b LIMIT 1000", ['name' => 'Chris']);
$e = $stopwatch->stop("e");
var_dump($e->getDuration());

/*
$stopwatch->start("x");
for ($i = 0; $i < 10; ++$i) {
    $result = $session->run("MATCH (n:Person) RETURN n LIMIT 100");
}
$e = $stopwatch->stop("x");
echo $e->getDuration();
*/