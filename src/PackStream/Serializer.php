<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\PackStream;

use GraphAware\Bolt\PackStream\Structure\MessageStructure;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;
use GraphAware\Bolt\Protocol\Message\FailureMessage;
use GraphAware\Bolt\Protocol\Message\RawMessage;
use GraphAware\Bolt\Protocol\Message\RecordMessage;
use GraphAware\Bolt\Protocol\Message\SuccessMessage;

class Serializer
{
    /**
     * @var Packer
     */
    protected $packer;

    /**
     * @var Unpacker
     */
    protected $unpacker;

    /**
     * Serializer constructor.
     *
     * @param Packer   $packer
     * @param Unpacker $unpacker
     */
    public function __construct(Packer $packer, Unpacker $unpacker)
    {
        $this->packer = $packer;
        $this->unpacker = $unpacker;
    }

    /**
     * @param AbstractMessage $message
     */
    public function serialize(AbstractMessage $message)
    {
        $buffer = '';
        $buffer .= $this->packer->packStructureHeader($message->getFieldsLength(), $message->getSignature());

        foreach ($message->getFields() as $field) {
            $buffer .= $this->packer->pack($field);
        }

        $message->setSerialization($buffer);
    }

    /**
     * @param RawMessage $message
     *
     * @return Structure\Structure
     */
    public function deserialize(RawMessage $message)
    {
        return $this->unpacker->unpackRaw($message);
    }

    /**
     * @param MessageStructure $structure
     * @param RawMessage       $rawMessage
     *
     * @return SuccessMessage
     */
    public function convertStructureToSuccessMessage(MessageStructure $structure, RawMessage $rawMessage)
    {
        $message = new SuccessMessage($structure->getElements()[0]);
        $message->setSerialization($rawMessage->getBytes());

        return $message;
    }

    /**
     * @param MessageStructure $structure
     * @param RawMessage       $rawMessage
     *
     * @return RecordMessage
     */
    public function convertStructureToRecordMessage(MessageStructure $structure, RawMessage $rawMessage)
    {
        $message = new RecordMessage($structure->getElements()[0]);
        $message->setSerialization($rawMessage->getBytes());

        return $message;
    }

    /**
     * @param MessageStructure $structure
     * @param RawMessage       $rawMessage
     *
     * @return FailureMessage
     */
    public function convertStructureToFailureMessage(MessageStructure $structure, RawMessage $rawMessage)
    {
        $message = new FailureMessage($structure->getElements()[0]);
        $message->setSerialization($rawMessage->getBytes());

        return $message;
    }
}
