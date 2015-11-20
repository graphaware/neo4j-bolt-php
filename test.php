<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$bolt = new Bolt('localhost', 7687);
$stopwatch = new Stopwatch();

$session = $bolt->getSession();
$session->run("MATCH (n) DETACH DELETE n");
$session->run("CREATE (n:Node) SET n.prop = {f} RETURN n.prop", ['f' => pi()]);