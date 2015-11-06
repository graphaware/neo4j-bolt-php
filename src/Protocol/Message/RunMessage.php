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

class RunMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'RUN';

    protected $statement;

    protected $params;

    public function __construct($statement, array $params = array())
    {
        parent::__construct(Constants::SIGNATURE_RUN);
        $this->fields = array($statement, $params);
        $this->statement = $statement;
        $this->params = $params;
    }

    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

    public function getFields()
    {
        return array($this->statement, $this->params);
    }

    public function getStatement()
    {
        return $this->statement;
    }

    public function getParams()
    {
        return $this->params;
    }
}