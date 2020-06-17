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

use PTS\Bolt\Exception\BoltInvalidArgumentException;
use PTS\Bolt\Protocol\Message\PullAllMessage;
use PTS\Bolt\Protocol\Message\RunMessage;
use PTS\Bolt\Protocol\V1\Session;
use GraphAware\Common\Driver\PipelineInterface;
use GraphAware\Common\Result\ResultCollection;

class Pipeline implements PipelineInterface
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
        $this->messages[] = new RunMessage($query, $parameters, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $resultCollection = new ResultCollection();

        foreach ($this->messages as $message) {
            $result = $this->session->run($message->getStatement(), $message->getParams(), $message->getTag());
            $resultCollection->add($result);
        }

        return $resultCollection;
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
}
