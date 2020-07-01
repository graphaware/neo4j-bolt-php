<?php

namespace PTS\Bolt\Protocol;

use GraphAware\Common\Driver\PipelineInterface as DriverPipelineInterface;

interface PipelineInterface extends DriverPipelineInterface
{
    /**
     * @param string $query
     * @param array  $parameters
     * @param null   $tag
     */
    public function push($query, array $parameters = [], $tag = null);

    /**
     * @return \GraphAware\Common\Result\ResultCollection
     */
    public function run();

    /**
     * @return PipelineMessage[]
     */
    public function getMessages(): array;
}
