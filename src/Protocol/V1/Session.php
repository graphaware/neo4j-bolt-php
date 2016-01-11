<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\V1;

use GraphAware\Bolt\Protocol\AbstractSession;
use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Protocol\Pipeline;
use GraphAware\Bolt\Driver;

class Session extends AbstractSession
{
    const PROTOCOL_VERSION = 1;

    protected $connection;

    protected $isInitialized = false;

    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }

    public function __construct(\GraphAware\Bolt\IO\AbstractIO $io, \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher)
    {
        parent::__construct($io, $dispatcher);
        $this->connection = new Connection($this->io);
    }

    /**
     * @param $statement
     * @param array $parameters
     * @param bool|true $autoReceive
     * @return \GraphAware\Bolt\Result\Result
     * @throws \Exception
     */
    public function run($statement, array $parameters = array(), $discard = false, $autoReceive = true)
    {
        $runResponse = new Response($this->connection);
        $runResponse->registerCallback(Response::ON_SUCCESS, function($v) {
            echo 'onSuccess';
        });

        $pullAllResponse = new Response($this->connection);
        $pullAllResponse->registerCallback(Response::ON_RECORD, function($v) {
            echo 'received record' . PHP_EOL;
        });

        $this->connection->add(Constants::SIGNATURE_RUN, array($statement, $parameters), $runResponse);
        $this->connection->add(Constants::SIGNATURE_PULL_ALL, array(), $pullAllResponse);
        $this->connection->send();

        while (!$pullAllResponse->isCompleted()) {
            $this->connection->fetchNext();
        }
        $this->connection->close();
    }

    public function runPipeline(Pipeline $pipeline)
    {
        // TODO: Implement runPipeline() method.
    }

    public function createPipeline()
    {
        // TODO: Implement createPipeline() method.
    }
}