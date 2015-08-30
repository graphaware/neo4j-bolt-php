<?php

namespace GraphAware\Bolt\PackStream;

use GraphAware\Bolt\PackStream\Structure\MessageStructure;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;
use GraphAware\Bolt\Protocol\Message\RawMessage;
use GraphAware\Bolt\Protocol\Message\RecordMessage;
use GraphAware\Bolt\Protocol\Message\SuccessMessage;
use Neo4j\PackStream\PackStream\Packer;

class Serializer
{
    /**
     * @var \Neo4j\PackStream\PackStream\Packer
     */
    protected $packer;

    /**
     * @var \GraphAware\Bolt\PackStream\Unpacker
     */
    protected $unpacker;

    /**
     * @param \Neo4j\PackStream\PackStream\Packer  $packer
     * @param \GraphAware\Bolt\PackStream\Unpacker $unpacker
     */
    public function __construct(Packer $packer, Unpacker $unpacker)
    {
        $this->packer = $packer;
        $this->unpacker = $unpacker;
    }

    public function serialize(AbstractMessage $message)
    {
        $buffer = '';
        $buffer .= $this->packStructureHeader($message->getFieldsLength(), $message->getSignature());
        $buffer .= $this->packFields($message->getFields());

        $message->setSerialization($buffer);
    }


    public function deserialize(RawMessage $message)
    {
        $structure = $this->unpacker->unpackRaw($message);
        if ($structure->isSuccess()) {
            return $this->convertStructureToSuccessMessage($structure, $message);
        } elseif ($structure->isRecord()) {
            return $this->convertStructureToRecordMessage($structure, $message);
        }

        return $structure;
    }

    public function convertStructureToSuccessMessage(MessageStructure $structure, RawMessage $rawMessage)
    {
        $message = new SuccessMessage($structure->getElements()[0]);
        $message->setSerialization($rawMessage->getBytes());

        return $message;
    }

    public function convertStructureToRecordMessage(MessageStructure $structure, RawMessage $rawMessage)
    {
        $message = new RecordMessage($structure->getElements()[0]);
        $message->setSerialization($rawMessage->getBytes());
        //print_r($structure);

        return $message;
    }

    public function packStructureHeader($length, $signature)
    {
        $stream = '';
        $stream .= $this->packer->getStructureMarker($length);
        $stream .= chr($signature);

        return $stream;
    }

    public function packFields(array $fields)
    {
        $stream = '';
        foreach ($fields as $field) {
            $stream .= $this->packer->pack($field);
        }

        return $stream;
    }
}