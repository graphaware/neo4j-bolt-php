<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol\V3;

use PTS\Bolt\Driver;
use PTS\Bolt\Protocol\Message\V3\BeginMessage;
use PTS\Bolt\Protocol\Message\V3\CommitMessage;
use PTS\Bolt\Protocol\Message\V3\GoodbyeMessage;
use PTS\Bolt\Protocol\Message\V3\HelloMessage;
use PTS\Bolt\Protocol\Message\V3\RollbackMessage;
use PTS\Bolt\Protocol\Message\V3\RunMessageWithMetadata;

class Session extends \PTS\Bolt\Protocol\V1\Session
{
    const PROTOCOL_VERSION = 3;

    /**
     * {@inheritdoc}
     */
    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }

    protected function createRunMessage($statement, $prams = [])
    {
        // Bolt V3+ uses run messages with metadata
        return new RunMessageWithMetadata($statement, $prams);
    }

    /**
     * {@inheritdoc}
     */
    public function transaction()
    {
        if ($this->transaction instanceof Transaction) {
            throw new \RuntimeException('A transaction is already bound to this session');
        }

        return new Transaction($this);
    }

    public function close()
    {
        $this->goodbye();
        parent::close();
    }

    public function hello()
    {
        $this->io->assertConnected();
        $ua = Driver::getUserAgent();
        $this->sendMessage(new HelloMessage($ua, $this->credentials));
        $responseMessage = $this->fetchResponse();
        if (!$responseMessage->isSuccess()) {
            throw new \Exception('Unable to HELLO');
        }
        $this->isInitialized = true;
    }

    public function init()
    {
        // v3+ init is replaced with hello
        $this->hello();
    }


    public function begin()
    {
        $this->sendMessage(new BeginMessage());
        $this->fetchResponse();
    }

    public function commit()
    {
        $this->sendMessage(new CommitMessage());
        $this->fetchResponse();
    }

    public function rollback()
    {
        $this->sendMessage(new RollbackMessage());
        $this->fetchResponse();
    }

    public function goodbye()
    {
        $this->sendMessage(new GoodbyeMessage());
    }
}
