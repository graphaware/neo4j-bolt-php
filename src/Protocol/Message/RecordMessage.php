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

use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Common\Result\RecordViewInterface;

class RecordMessage extends AbstractMessage implements RecordViewInterface
{
    const MESSAGE_TYPE = 'RECORD';

    protected $values;

    public function __construct($list)
    {
        parent::__construct(Constants::SIGNATURE_RECORD);
        $this->values = $list;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function keys()
    {
        // TODO: Implement keys() method.
    }

    public function hasValues()
    {
        // TODO: Implement hasValues() method.
    }

    public function value($key)
    {
        // TODO: Implement value() method.
    }

    public function values()
    {
        // TODO: Implement values() method.
    }

    public function valueByIndex($index)
    {
        // TODO: Implement valueByIndex() method.
    }

    public function record()
    {
        // TODO: Implement record() method.
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        // TODO: Implement get() method.
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue($key)
    {
        // TODO: Implement hasValue() method.
    }

    /**
     * {@inheritdoc}
     */
    public function nodeValue($key)
    {
        // TODO: Implement nodeValue() method.
    }

    /**
     * {@inheritdoc}
     */
    public function relationshipValue($key)
    {
        // TODO: Implement relationshipValue() method.
    }

    /**
     * {@inheritdoc}
     */
    public function pathValue($key)
    {
        // TODO: Implement pathValue() method.
    }

    /**
     * {@inheritdoc}
     */
    public function getByIndex($index)
    {
        // TODO: Implement getByIndex() method.
    }
}
