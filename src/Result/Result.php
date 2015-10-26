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

use GraphAware\Bolt\PackStream\Structure\ListCollection;
use GraphAware\Bolt\Protocol\Message\RecordMessage;

class Result
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

    public function addRecord(RecordMessage $recordMessage)
    {
        $values = $recordMessage->getValues();
        $fields = $this->fields->getList();
        $rec = [];
        foreach ($fields as $k => $field) {
            $rec[$field->getValue()] = $values[$k]->getValue();
        }
        $this->records[] = $rec;
    }

    /**
     * @param \GraphAware\Bolt\PackStream\Structure\ListCollection|array $fields
     */
    public function setFields($fields)
    {
        if (!is_array($fields) && !$fields instanceof ListCollection) {
            throw new \InvalidArgumentException('fields should be an array or an instance of fields collection');
        }
        $this->fields = $fields;
    }

    public function setStatistics(array $stats)
    {
        $this->statistics = $stats;
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

    public function setType($type)
    {
        $this->type = $type;
    }
}