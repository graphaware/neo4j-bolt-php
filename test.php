<?php

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Stopwatch\Stopwatch;


$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();
$i = 0;
$stopwatch = new Stopwatch();
$stopwatch->start("e");
$result = $session->run("MATCH (n:User) RETURN n LIMIT 100000");
$e = $stopwatch->stop("e");
var_dump($e->getDuration());