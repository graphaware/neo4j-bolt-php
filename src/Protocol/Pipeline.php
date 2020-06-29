<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol;

use Exception\PipelineFinishedException;
use PTS\Bolt\Exception\BoltInvalidArgumentException;
use PTS\Bolt\Protocol\Message\PullAllMessage;
use PTS\Bolt\Protocol\Message\RunMessage;
use PTS\Bolt\Protocol\V1\Session;
use GraphAware\Common\Driver\PipelineInterface;
use GraphAware\Common\Result\ResultCollection;

class Pipeline implements PipelineInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    protected $completed = false;

    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function push($query, array $parameters = [], $tag = null)
    {
        if (null === $query) {
            throw new BoltInvalidArgumentException('Statement cannot be null');
        }
        if ($this->completed) {
            throw new PipelineFinishedException('Pipeline has completed');
        }
        $this->session->runQueued($query, $parameters);
        $this->messages[] = ['query' => $query, 'parameters' => $parameters, 'tag' => $tag];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $resultCollection = new ResultCollection();
        $this->session->flushQueue();
        $numOfResults = sizeof($this->messages);
        foreach ($this->messages as $message) {
            $result = $this->session->fetchRunResult(
                $message['query'],
                $message['parameters'],
                $message['tag']
            );
            $resultCollection->add($result);
        }
        $this->completed = true;
        return $resultCollection;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->messages);
    }
}
