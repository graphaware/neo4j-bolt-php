<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol;

use GraphAware\Bolt\Protocol\Message\PullAllMessage;
use GraphAware\Bolt\Protocol\Message\RunMessage;
use GraphAware\Bolt\Protocol\V1\Session;
use GraphAware\Common\Result\ResultCollection;
use GraphAware\Neo4j\Client\HttpDriver\Pipeline as BasePipeline;

class Pipeline extends BasePipeline
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var RunMessage[]
     */
    protected $messages = [];

    /**
     * @param Session $session
     */
    public function __construct(Session $session)
    {
        parent::__construct($session);
    }

    /**
     * @param string $query
     * @param array  $parameters
     * @param null   $tag
     */
    public function push($query, array $parameters = array(), $tag = null)
    {
        $this->messages[] = new RunMessage($query, $parameters, $tag);
    }

    /**
     * @return RunMessage[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->messages);
    }

    /**
     * @return ResultCollection
     */
    public function run()
    {
        $pullAllMessage = new PullAllMessage();
        $batch = [];
        $resultCollection = new ResultCollection();

        foreach ($this->messages as $message) {
            $batch[] = $message;
            $batch[] = $pullAllMessage;
        }

        $this->session->sendMessages($batch);

        foreach ($this->messages as $message) {
            $resultCollection->add($this->session->recv($message->getStatement(), $message->getParams(), $message->getTag()), $message->getTag());
        }

        return $resultCollection;
    }
}
