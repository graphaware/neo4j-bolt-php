<?php

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

    public function getFields()
    {
        return $this->fields;
    }

    public function getRecords()
    {
        return $this->records;
    }
}