<?php

namespace GraphAware\Bolt\Result;

use GraphAware\Bolt\PackStream\Structure\ListCollection;
use GraphAware\Bolt\Protocol\Message\RecordMessage;

class Result
{
    protected $records = [];

    public function addRecord(ListCollection $fieldsList, RecordMessage $recordMessage)
    {
        $values = $recordMessage->getValues();
        $fields = $fieldsList->getList();
        $rec = [];
        foreach ($fields as $k => $field) {
            $rec[$field->getValue()] = $values[$k]->getValue();
        }
        $this->records[] = $rec;
    }
}