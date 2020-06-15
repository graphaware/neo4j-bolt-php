<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\Message\V3;

use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;

class RunMessageWithMetadata extends AbstractMessage
{
    const MESSAGE_TYPE = 'RUN';

    /**
     * @var string
     */
    protected $statement;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var null|string
     */
    protected $tag;
    /**
     * @var array
     */
    protected $meta;

    /**`
     * @param string $statement
     * @param array $params
     * @param null|string $tag
     * @param array $meta
     */
    public function __construct($statement, array $params = array(), $tag = null, array $meta = [])
    {
        parent::__construct(Constants::SIGNATURE_RUN);
        $this->fields = array($statement, $params);
        $this->statement = $statement;
        $this->params = $params;
        $this->tag = $tag;
        $this->meta = $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

    /**
     * {@inheritdoc}
     */
    public function getFields()
    {
        return ['statement' => $this->statement, 'parameters' => $this->params, 'metadata' => $this->meta];
    }

    /**
     * @return string
     */
    public function getStatement()
    {
        return $this->statement;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return null|string
     */
    public function getTag()
    {
        return $this->tag;
    }
}
