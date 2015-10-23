<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use GraphAware\Bolt\Driver;
use GraphAware\Bolt\Exception\BoltExceptionInterface;

class Run extends Command
{

    protected $args;

    protected function configure()
    {
        $this->setName('bolt:run')
            ->setDescription('Run a Cypher query with the Bolt binary protocol')
            ->addArgument(
                'statement',
                InputArgument::REQUIRED,
                'The Cypher Statement to run'
            )
            ->addOption(
                'params', 'p',
                InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,
                'parameters for the statement'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $p = [];
        $params = $input->getOption('params');
        foreach ($params as $param) {
            $split = explode(':', $param);
            $k = $split[0];
            $v = $split[1];
            if (is_numeric($v)) {
                $v = (int) $v;
            }
            $p[$k] = $v;
        }

        $driver = new Driver('localhost', 7687);
        $session = $driver->getSession();
        $statement = $input->getArgument('statement');
        try {
            $result = $session->run($statement, $p);
        } catch (BoltExceptionInterface $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }

        $table = new Table($output);
        $table->setHeaders($result->getFields()->getList());
        $table->setRows($result->getRecords());
        $table->render();
    }

    public function setArgv(array $args)
    {
        $this->args = $args;
    }
}