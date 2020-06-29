<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol;

use GraphAware\Common\Driver\PipelineInterface;
use GraphAware\Common\Driver\SessionInterface as BaseSessionInterface;
use GraphAware\Common\Transaction\TransactionInterface;
use PTS\Bolt\Result\Result;

interface SessionInterface extends BaseSessionInterface
{
    /**
     * @return string
     */
    public static function getProtocolVersion();

    /**
     * @param string $statement
     * @param array $parameters
     * @param null|string $tag
     *
     * @return \PTS\Bolt\Result\Result
     */
    public function run($statement, array $parameters = [], $tag = null);

    /**
     * Fetch Result from query
     * @param string $statement
     * @param array $parameters
     * @param null|string $tag
     *
     * @return \PTS\Bolt\Result\Result
     */
    public function fetchRunResult($statement, array $parameters = [], $tag = null): Result;

    /**
     * Queue query
     * @param $statement
     * @param array $parameters
     * @return void
     */
    public function runQueued($statement, array $parameters = []);
    

    /**
     * Flush message queue
     * @return void
     */
    public function flushQueue();
 

    /**
     * @param PipelineInterface $pipeline
     *
     * @return mixed
     */
    public function runPipeline(PipelineInterface $pipeline);

    /**
     * @param null|string $query
     * @param array $parameters
     * @param null|string $tag
     *
     * @return PipelineInterface
     */
    public function createPipeline($query = null, array $parameters = [], $tag = null);

    /**
     * @return TransactionInterface
     */
    public function transaction();

    /**
     * Begin transaction
     * @return void
     */
    public function begin();

    /**
     * Commit transaction
     * @return void
     */
    public function commit();

    /**
     * Rollback transaction
     * @return void
     */
    public function rollback();
}
