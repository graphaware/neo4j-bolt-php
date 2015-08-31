#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use GraphAware\Bolt\Console\Run;
use Symfony\Component\Console\Application;

$application = new Application();
$run = new Run();
$application->add($run);
$application->run();