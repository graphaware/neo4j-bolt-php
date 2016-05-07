<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol;

use GraphAware\Common\Driver\SessionInterface as BaseSessionInterface;

interface SessionInterface extends BaseSessionInterface
{
    /**
     * @return string
     */
    public static function getProtocolVersion();

    /**
     * @param string      $statement
     * @param array       $parameters
     * @param null|string $tag
     *
     * @return \GraphAware\Bolt\Result\Result
     */
    public function run($statement, array $parameters = array(), $tag = null);

    /**
     * @param Pipeline $pipeline
     *
     * @return mixed
     */
    public function runPipeline(Pipeline $pipeline);

    /**
     * @param null|string $query
     * @param array       $parameters
     * @param null|string $tag
     *
     * @return Pipeline
     */
    public function createPipeline($query = null, array $parameters = array(), $tag = null);

    /**
     * @return \GraphAware\Bolt\Protocol\V1\Transaction
     */
    public function transaction();
}
