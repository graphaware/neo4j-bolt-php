<?php

namespace GraphAware\Bolt\Result;

use GraphAware\Bolt\PackStream\Structure\ListCollection;
use GraphAware\Bolt\Protocol\Message\RecordMessage;

class Result
{
    protected $records = [];

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\ListCollection
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

    public function setFields(ListCollection $fields)
    {
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