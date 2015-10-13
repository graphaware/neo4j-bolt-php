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

class Pipeline
{
    /**
     * @var \GraphAware\Bolt\Protocol\SessionInterface
     */
    protected $session;

    /**
     * @var \GraphAware\Bolt\Protocol\Message\AbstractMessage[]
     */
    protected $messages = [];

    public function __construct(SessionInterface $session)
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
        $this->messages[] = new DiscardAllMessage();
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

    public function flush()
    {
        if (!$this->session->isInitialized) {
            $this->session->init();
        }

        $response = new Result();

        $this->session->sendMessages($this->messages);
        foreach ($this->messages as $message) {
            $hasMore = true;
            while ($hasMore) {
                $responseMessage = $this->session->receiveMessage();
                if ($responseMessage->isSuccess()) {
                    $hasMore = false;
                } elseif ($responseMessage->isRecord()) {
                    $response->addRecord($responseMessage);
                } elseif ($responseMessage->isFailure()) {
                }
            }
        }

        return $response;
    }
}