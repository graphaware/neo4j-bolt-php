<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol\Message;

use PTS\Bolt\Protocol\Constants;

class RunMessage extends AbstractMessage
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
     * @param string $statement
     * @param array $params
     * @param null|string $tag
     */
    public function __construct($statement, array $params = [], $tag = null)
    {
        parent::__construct(Constants::SIGNATURE_RUN);
        $this->fields = [$statement, $params];
        $this->statement = $statement;
        $this->params = $params;
        $this->tag = $tag;
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
        return [$this->statement, $this->params];
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
