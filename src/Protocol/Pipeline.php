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

use Doctrine\Common\Collections\ArrayCollection;

class Pipeline
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection|\GraphAware\Bolt\Protocol\Message[]
     */
    protected $messages;

    /**
     *
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\GraphAware\Bolt\Protocol\Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param \GraphAware\Bolt\Protocol\Message $message
     */
    public function addMessage(Message $message)
    {
        $this->messages->add($message);
    }

    /**
     * @param \GraphAware\Bolt\Protocol\Message[] $messages
     */
    public function addMessages(array $messages)
    {
        foreach ($messages as $message) {
            if (!$message instanceof Message) {
                throw new \InvalidArgumentException('Method Pipeline#addMessages accepts only Message instances');
            }

            $this->messages->add($message);
        }
    }
}