<?php

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Stopwatch\Stopwatch;

$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();
$i = 0;
while ($i < 10) {
    $stopwatch = new Stopwatch();

    $stopwatch->start("e");
    $result = $session->run("MATCH (n:Person {name: {name} }) RETURN n LIMIT 1000", ['name' => 'chris']);
    $e = $stopwatch->stop("e");
    var_dump($e->getDuration());
    ++$i;
}


//print_r($result);