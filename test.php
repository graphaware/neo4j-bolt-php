<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();

$stopwatch = new Stopwatch();

$pipeline = $session->createPipeline();
$pipeline->push("MATCH (n) RETURN count(n)", array(), 'engine1');

$results = $pipeline->run();

print_r($results->get('engine1'));