<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;

$bolt = new Bolt('localhost', 7687);

$session = $bolt->getSession();
$response = $session->run('MATCH (n:Person) WHERE HAS(n.name) RETURN n, ({year} - n.born) as age LIMIT 5', array('year' => 2015));
//print_r($response);