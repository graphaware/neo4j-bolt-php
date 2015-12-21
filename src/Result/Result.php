<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Result;

use GraphAware\Bolt\PackStream\Structure\AbstractElement;
use GraphAware\Bolt\PackStream\Structure\ListCollection;
use GraphAware\Bolt\PackStream\Structure\ListStructure;
use GraphAware\Common\Cypher\StatementInterface;
use GraphAware\Common\Result\AbstractResultCursor;
use GraphAware\Common\Result\RecordViewInterface;
use GraphAware\Common\Result\ResultCursorInterface;
use GraphAware\Common\Result\StatementStatistics;

class Result extends AbstractResultCursor
{
    protected $records = [];

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\ListCollection|array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $statistics = [];

    protected $type;

    protected $summary;

    public function __construct(StatementInterface $statement)
    {
        $this->summary = new ResultSummary($statement);
        return parent::__construct($statement);
    }

    public function addRecord($recordMessage)
    {
        $this->records[] = $recordMessage;
    }

    /**
     * @param \GraphAware\Bolt\PackStream\Structure\ListCollection|array $fields
     */
    public function setFields($fields)
    {
        if (!is_array($fields) && !$fields instanceof ListStructure) {
            throw new \InvalidArgumentException('fields should be an array or an instance of fields collection');
        }
        $this->fields = $fields;
    }

    public function setStatistics(array $stats)
    {
        $this->statistics = $stats;
        $this->summary->setStatistics($stats);
    }

    public function getStatistics()
    {
        return $this->statistics;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getRecords()
    {
        return $this->records;
    }

    public function getRecord()
    {
        if (count($this->records) < 1) {
            throw new \InvalidArgumentException('No records');
        }

        return $this->records[0];
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function summarize()
    {
        return $this->summary;
    }

    /**
     * @return \GraphAware\Common\Result\StatementStatistics
     */
    public function updateStatistics()
    {
        return $this->statistics;
    }

    public function statementType()
    {
        return $this->type;
    }

    public function hasSummary()
    {
        //
    }

    public function position()
    {
        // TODO: Implement position() method.
    }

    public function skip()
    {
        // TODO: Implement skip() method.
    }
}