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

use GraphAware\Bolt\PackStream\Structure\Structure;
use GraphAware\Bolt\Record\RecordView;
use GraphAware\Common\Cypher\StatementInterface;
use GraphAware\Common\Result\AbstractRecordCursor;
use GraphAware\Common\Result\RecordViewInterface;
use GraphAware\Common\Result\RecordCursorInterface;
use GraphAware\Common\Result\StatementStatistics;

class Result extends AbstractRecordCursor
{
    /**
     * @var \GraphAware\Common\Result\RecordViewInterface[]
     */
    protected $records = [];

    /**
     * @var array
     */
    protected $fields;

    /**
     * Result constructor.
     * @param \GraphAware\Common\Cypher\StatementInterface $statement
     */
    public function __construct(StatementInterface $statement)
    {
        $this->resultSummary = new ResultSummary($statement);
        return parent::__construct($statement);
    }

    /**
     * @param \GraphAware\Bolt\PackStream\Structure\Structure $structure
     */
    public function pushRecord(Structure $structure)
    {
        $this->records[] = new RecordView($this->fields, $structure->getElements());
    }

    /**
     * @return \GraphAware\Common\Result\RecordViewInterface[]
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return \GraphAware\Common\Result\RecordViewInterface
     */
    public function getRecord()
    {
        if (count($this->records) < 1) {
            throw new \InvalidArgumentException('No records');
        }

        return $this->records[0];
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param array $stats
     */
    public function setStatistics(array $stats)
    {
        $this->resultSummary->setStatistics($stats);
    }

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \GraphAware\Bolt\Result\ResultSummary
     */
    public function summarize()
    {
        return $this->resultSummary;
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