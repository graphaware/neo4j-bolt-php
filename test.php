<?php

require_once(__DIR__.'/vendor/autoload.php');

use GraphAware\Bolt\Driver as Bolt;
use Symfony\Component\Stopwatch\Stopwatch;

$bolt = new Bolt('localhost', 7687);
$stopwatch = new Stopwatch();

$session = $bolt->getSession();
$session->run("MATCH (n) DETACH DELETE n");

$q = 'UNWIND {props} as prop MERGE (n:Person {login: prop.login}) SET n.name = prop.name';
$session->run($q, ['props' => [
    [
        'login' => 'login1',
        'name' => 'name1'
    ],
    [
        'login' => 'login2',
        'name' => 'name2'
    ]
]]);
//print_r($result);