<?php

namespace GraphAware\Bolt\Result;

use GraphAware\Common\Cypher\StatementInterface;
use GraphAware\Common\Result\SummaryInterface;

class ResultSummary implements SummaryInterface
{
    /**
     * @var \GraphAware\Common\Cypher\StatementInterface $statement
     */
    protected $statement;

    /**
     * @param \GraphAware\Common\Cypher\StatementInterface $statement
     */
    public function __construct(StatementInterface $statement)
    {
        $this->statement = $statement;
    }

    /**
     * @return \GraphAware\Common\Cypher\StatementInterface $statement
     */
    public function statement()
    {
        return $this->statement;
    }
}