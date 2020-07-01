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

use PTS\Bolt\Exception\PipelineFinishedException;
use PTS\Bolt\Protocol\PipelineInterface;
use PTS\Bolt\Exception\BoltInvalidArgumentException;

class Pipeline implements PipelineInterface
{
    /**
     * @var SessionInterface
     */
    protected $session;

    protected $completed = false;

    /**
     * @var PipelineMessage[]
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
    public function push($query, array $parameters = [], $tag = '')
    {
        if (!$query) {
            throw new BoltInvalidArgumentException('Statement cannot be null');
        }
        if ($this->completed) {
            throw new PipelineFinishedException('Pipeline has completed');
        }
        $this->messages[] = new PipelineMessage($query, $parameters, $tag);
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ($this->completed) {
            throw new PipelineFinishedException('Pipeline has completed');
        }
        $this->completed = true;
        return $this->session->runPipeline($this);
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->messages);
    }

    /**
     * @return PipelineMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
