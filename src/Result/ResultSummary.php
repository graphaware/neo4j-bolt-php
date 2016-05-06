<?php

namespace GraphAware\Bolt\Result;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Common\Cypher\StatementInterface;
use GraphAware\Common\Result\StatementStatistics;
use GraphAware\Common\Result\ResultSummaryInterface;

class ResultSummary implements ResultSummaryInterface
{
    /**
     * @var Statement
     */
    protected $statement;

    /**
     * @var StatementStatistics|null
     */
    protected $updateStatistics;

    /**
     * @param StatementInterface $statement
     */
    public function __construct(StatementInterface $statement)
    {
        $this->statement = $statement;
    }

    /**
     * @return StatementInterface
     */
    public function statement()
    {
        return $this->statement;
    }

    /**
     * @return StatementStatistics|null
     */
    public function updateStatistics()
    {
        return $this->updateStatistics;
    }

    /**
     * @return \GraphAware\Common\Cypher\StatementType
     */
    public function statementType()
    {
        return $this->statement->statementType();
    }

    /**
     * @param array $stats
     */
    public function setStatistics(array $stats)
    {
        // Difference between http format and binary format of statistics
        foreach ($stats as $k => $v) {
            $nk = str_replace('-', '_', $k);
            $stats[$nk] = $v;
            unset($stats[$k]);
        }

        $this->updateStatistics = new StatementStatistics($stats);
    }

    public function notifications()
    {
        // TODO: Implement notifications() method.
    }
}
