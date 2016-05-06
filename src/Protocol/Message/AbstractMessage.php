<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\Message;

abstract class AbstractMessage implements MessageInterface
{
    /**
     * @var string
     */
    protected $signature;

    /**
     * @var array
     */
    protected $fields = [];

    /**
     * @var bool
     */
    protected $isSerialized = false;

    /**
     * @var null
     */
    protected $serialization = null;

    /**
     * @param string $signature
     * @param array  $fields
     */
    public function __construct($signature, array $fields = array())
    {
        $this->signature = $signature;
        $this->fields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return $this->fields;
    }

    public function getFieldsLength()
    {
        return count($this->fields);
    }

    public function setSerialization($stream)
    {
        $this->serialization = $stream;
        $this->isSerialized = true;
    }

    public function getSerialization()
    {
        return $this->serialization;
    }

    /**
     * {@inheritdoc}
     */
    public function isSuccess()
    {
        return $this->getMessageType() === 'SUCCESS';
    }

    /**
     * {@inheritdoc}
     */
    public function isFailure()
    {
        return $this->getMessageType() === 'FAILURE';
    }

    /**
     * @return bool
     */
    public function isRecord()
    {
        return $this->getMessageType() === 'RECORD';
    }

    /**
     * @return bool
     */
    public function hasFields()
    {
        return !empty($this->fields);
    }
}
