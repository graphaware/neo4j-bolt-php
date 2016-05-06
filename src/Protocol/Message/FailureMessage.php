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

class FailureMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'FAILURE';

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param $map
     */
    public function __construct($map)
    {
        parent::__construct(Constants::SIGNATURE_FAILURE);
        $this->code = $map->get('code')->__toString();
        $this->message = $map->get('message')->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getFullMessage()
    {
        return $this->code.' : '.$this->message;
    }
}
