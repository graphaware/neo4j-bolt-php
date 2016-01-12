<?php

require_once(__DIR__.'/vendor/autoload.php');

use Symfony\Component\Stopwatch\Stopwatch;

$driver = \GraphAware\Bolt\GraphDatabase::driver("bolt://localhost");
$session = $driver->session();

$stopwatch = new Stopwatch();

$pipeline = $session->createPipeline();
$pipeline->push("MATCH (n) RETURN count(n)", array(), 'engine1');

$results = $pipeline->run();
$session->close();


$transaction = $session->transaction();
$transaction->begin();
$transaction->run(\GraphAware\Common\Cypher\Statement::create("MATCH (n:Chris) DELETE n"));
$transaction->run(\GraphAware\Common\Cypher\Statement::create("CREATE (n:Chris)"));
$transaction->commit();

$transaction = $session->transaction();
$transaction->begin();
$transaction->run(\GraphAware\Common\Cypher\Statement::create("CREATE (n:Chris)"));
$transaction->rollback();