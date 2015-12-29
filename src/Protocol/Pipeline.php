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

use GraphAware\Bolt\Protocol\Message\AbstractMessage;
use GraphAware\Bolt\Protocol\Message\DiscardAllMessage;
use GraphAware\Bolt\Protocol\Message\PullAllMessage;
use GraphAware\Bolt\Protocol\Message\RunMessage;
use GraphAware\Bolt\Result\Result;
use GraphAware\Common\Cypher\Statement;
use GraphAware\Bolt\Protocol\V1\Session;

class Pipeline
{
    /**
     * @var \GraphAware\Bolt\Protocol\V1\Session
     */
    protected $session;

    /**
     * @var \GraphAware\Bolt\Protocol\Message\AbstractMessage[]
     */
    protected $messages = [];

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param string $query
     * @param array $parameters
     */
    public function push($query, array $parameters = array())
    {
        $this->messages[] = new RunMessage($query, $parameters);
        $this->messages[] = new PullAllMessage();
    }

    /**
     * @return \GraphAware\Bolt\Protocol\Message\AbstractMessage[]
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

    public function run()
    {
        if (!$this->session->isInitialized) {
            $this->session->init();
        }

        $resultCursor = array();

        $this->session->sendMessages($this->messages);
        foreach ($this->messages as $k => $message) {
            if ($message instanceof RunMessage) {
                $result = new Result(Statement::create($message->getStatement(), $message->getParams()));
            }
            $hasMore = true;
            while ($hasMore) {
                $responseMessage = $this->session->receiveMessage();
                if ($responseMessage->isSuccess()) {
                    $hasMore = false;
                    if ($responseMessage->hasFields()) {
                        $result->setFields($responseMessage->getFields());
                    }
                    if ($responseMessage->hasStatistics()) {
                        $result->setStatistics($responseMessage->getStatistics());
                    }
                    if ($responseMessage->hasType()) {
                        $result->setType($responseMessage->getType());
                    }
                } elseif ($responseMessage->isRecord()) {
                    $result->pushRecord($responseMessage);
                } elseif ($responseMessage->isFailure()) {
                }
            }

            if ($message instanceof RunMessage) {
                $resultCursor[] = $result;
            }

        }

        return $resultCursor;
    }
}