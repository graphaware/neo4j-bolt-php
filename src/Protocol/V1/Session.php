<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\V1;

use GraphAware\Bolt\Driver;
use GraphAware\Bolt\PackStream\Unpacker;
use GraphAware\Bolt\Protocol\AbstractSession;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;
use GraphAware\Bolt\Protocol\Message\AckFailureMessage;
use GraphAware\Bolt\Protocol\Message\DiscardAllMessage;
use GraphAware\Bolt\Protocol\Message\InitMessage;
use GraphAware\Bolt\Protocol\Message\PullAllMessage;
use GraphAware\Bolt\Protocol\Message\RawMessage;
use GraphAware\Bolt\Protocol\Message\RunMessage;
use GraphAware\Bolt\Protocol\Pipeline;
use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Bolt\Result\Result;
use GraphAware\Common\Cypher\Statement;
use GraphAware\Bolt\PackStream\BytesWalker;

class Session extends AbstractSession
{
    const PROTOCOL_VERSION = 1;

    public $isInitialized = false;

    public $transaction;

    /**
     * @var \GraphAware\Bolt\PackStream\BytesWalker
     */
    private $bw;

    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }

    /**
     * @param $statement
     * @param array $parameters
     * @param bool|true $autoReceive
     * @return \GraphAware\Bolt\Result\Result
     * @throws \Exception
     */
    public function run($statement, array $parameters = array(), $tag = null)
    {
        $response = new Result(Statement::create($statement, $parameters));
        $messages = array(
            new RunMessage($statement, $parameters),
        );
        $messages[] = new PullAllMessage();

        if (!$this->isInitialized) {
            $this->init();
        }

        $this->sendMessages($messages);

        $bw = new BytesWalker($this->io);
        $unpacker = new Unpacker($bw);

        $runResponse = new Response();
        $r = $unpacker->unpack();
        if (is_array($r)) {
            print_r($r);
        }
        if ($r->isSuccess()) {
            $runResponse->onSuccess($r);
        }

        $pullResponse = new Response();
        $i = 1;
        while (!$pullResponse->isCompleted()) {
            $r = $unpacker->unpack();
            if(!is_object($r)) {
                print_r($r);
            }
            if ($r->isRecord()) {
                //echo 'record' . $i . PHP_EOL;
                ++$i;
                $pullResponse->onRecord($r);
            }
            if ($r->isSuccess()) {
                $pullResponse->onSuccess($r);
            }
        }

        //print_r($pullResponse);

        return $pullResponse;
    }

    public function init()
    {
        $this->io->assertConnected();
        $ua = Driver::getUserAgent();
        $this->sendMessage(new InitMessage($ua));
        $responseMessage = $this->receiveMessageInit();
        if ($responseMessage->getSignature() == "SUCCESS") {
            $this->isInitialized = true;
        } else {
            throw new \Exception('Unable to INIT');
        }
        $this->isInitialized = true;
    }

    public function runPipeline(Pipeline $pipeline)
    {

    }

    /**
     * @return \GraphAware\Bolt\Protocol\Pipeline
     */
    public function createPipeline()
    {
        return new Pipeline($this);
    }

    /**
     * @return \GraphAware\Bolt\PackStream\Structure\Structure
     */
    public function receiveMessageInit()
    {
        $bytes = '';

        $chunkHeader = $this->io->read(2);
        list(, $chunkSize) = unpack('n', $chunkHeader);
        $nextChunkLength = $chunkSize;
        do {
            if ($nextChunkLength) {
                $bytes .= $this->io->read($nextChunkLength);
            }
            list(, $next) = unpack('n', $this->io->read(2));
            $nextChunkLength = $next;
        } while($nextChunkLength > 0);

        $rawMessage = new RawMessage($bytes);

        $message = $this->serializer->deserialize($rawMessage);

        if ($message->getSignature() === "FAILURE") {
            $msg = sprintf('Neo4j Exception "%s" with code "%s"', $message->getElements()['message'], $message->getElements()['code']);
            $e = new MessageFailureException($msg);
            $e->setStatusCode($message->getElements()['code']);
            $this->sendMessage(new AckFailureMessage());

            throw $e;
        }

        return $message;
    }

    /**
     * @return \GraphAware\Bolt\PackStream\Structure\Structure
     */
    public function receiveMessage()
    {
        $bytes = '';

        $chunkHeader = $this->bw->read(2);
        list(, $chunkSize) = unpack('n', $chunkHeader);
        $nextChunkLength = $chunkSize;
        do {
            if ($nextChunkLength) {
                $bytes .= $this->bw->read($nextChunkLength);
            }
            list(, $next) = unpack('n', $this->bw->read(2));
            $nextChunkLength = $next;
        } while($nextChunkLength > 0);

        $rawMessage = new RawMessage($bytes);

        $message = $this->serializer->deserialize($rawMessage);

        if ($message->getSignature() === "FAILURE") {
            $msg = sprintf('Neo4j Exception "%s" with code "%s"', $message->getElements()['message'], $message->getElements()['code']);
            $e = new MessageFailureException($msg);
            $e->setStatusCode($message->getElements()['code']);
            $this->sendMessage(new AckFailureMessage());

            throw $e;
        }

        return $message;
    }

    /**
     * @param \GraphAware\Bolt\Protocol\Message\AbstractMessage $message
     */
    public function sendMessage(AbstractMessage $message)
    {
        $this->sendMessages(array($message));
    }

    /**
     * @param \GraphAware\Bolt\Protocol\Message\AbstractMessage[] $messages
     */
    public function sendMessages(array $messages)
    {
        foreach ($messages as $message) {
            $this->serializer->serialize($message);
        }

        $this->writer->writeMessages($messages);
    }

    /**
     * Closes this session and the corresponding connection to the socket
     */
    public function close()
    {
        //$this->io->close();
        //$this->isInitialized = false;
    }

    public function transaction()
    {
        if ($this->transaction instanceof Transaction) {
            throw new \RuntimeException('A transaction is already bound to this session');
        }

        return new Transaction($this);
    }
}