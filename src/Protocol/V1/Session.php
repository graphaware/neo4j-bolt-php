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
use GraphAware\Bolt\Exception\BoltInvalidArgumentException;
use GraphAware\Bolt\IO\AbstractIO;
use GraphAware\Bolt\Protocol\AbstractSession;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;
use GraphAware\Bolt\Protocol\Message\AckFailureMessage;
use GraphAware\Bolt\Protocol\Message\InitMessage;
use GraphAware\Bolt\Protocol\Message\PullAllMessage;
use GraphAware\Bolt\Protocol\Message\RawMessage;
use GraphAware\Bolt\Protocol\Message\RunMessage;
use GraphAware\Bolt\Protocol\Pipeline;
use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Bolt\Result\Result as CypherResult;
use GraphAware\Common\Cypher\Statement;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Session extends AbstractSession
{
    const PROTOCOL_VERSION = 1;

    /**
     * @var bool
     */
    public $isInitialized = false;

    /**
     * @var Transaction|null
     */
    public $transaction;

    /**
     * @var array
     */
    protected $credentials;

    /**
     * @param AbstractIO               $io
     * @param EventDispatcherInterface $dispatcher
     * @param array                    $credentials
     */
    public function __construct(AbstractIO $io, EventDispatcherInterface $dispatcher, array $credentials)
    {
        parent::__construct($io, $dispatcher);

        $this->credentials = $credentials;
        $this->init();
    }

    /**
     * {@inheritdoc}
     */
    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function run($statement, array $parameters = array(), $tag = null)
    {
        if (null === $statement) {
            //throw new BoltInvalidArgumentException("Statement cannot be null");
        }
        $messages = array(
            new RunMessage($statement, $parameters),
        );

        $messages[] = new PullAllMessage();
        $this->sendMessages($messages);

        $runResponse = new Response();
        $r = $this->unpacker->unpack();

        if ($r->isSuccess()) {
            $runResponse->onSuccess($r);
        } elseif ($r->isFailure()) {
            try {
                $runResponse->onFailure($r);
            } catch (MessageFailureException $e) {
                // server ignores the PULL ALL
                $this->handleIgnore();
                $this->sendMessage(new AckFailureMessage());
                // server success for ACK FAILURE
                $r2 = $this->handleSuccess();
                throw $e;
            }
        }

        $pullResponse = new Response();

        while (!$pullResponse->isCompleted()) {
            $r = $this->unpacker->unpack();

            if ($r->isRecord()) {
                $pullResponse->onRecord($r);
            }

            if ($r->isSuccess()) {
                $pullResponse->onSuccess($r);
            }

            if ($r->isFailure()) {
                $pullResponse->onFailure($r);
            }
        }

        $cypherResult = new CypherResult(Statement::create($statement, $parameters, $tag));
        $cypherResult->setFields($runResponse->getMetadata()[0]->getElements());

        foreach ($pullResponse->getRecords() as $record) {
            $cypherResult->pushRecord($record);
        }

        $pullMeta = $pullResponse->getMetadata();

        if (isset($pullMeta[0])) {
            if (isset($pullMeta[0]->getElements()['stats'])) {
                $cypherResult->setStatistics($pullResponse->getMetadata()[0]->getElements()['stats']);
            } else {
                $cypherResult->setStatistics([]);
            }
        }

        return $cypherResult;
    }

    /**
     * {@inheritdoc}
     */
    public function runPipeline(Pipeline $pipeline)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function createPipeline($query = null, array $parameters = [], $tag = null)
    {
        return new Pipeline($this);
    }

    /**
     * @param string      $statement
     * @param array       $parameters
     * @param null|string $tag
     *
     * @return CypherResult
     */
    public function recv($statement, array $parameters = array(), $tag = null)
    {
        $runResponse = new Response();
        $r = $this->unpacker->unpack();
        $shouldThrow = false;
        if ($r->isFailure()) {
            try {
                $runResponse->onFailure($r);
            } catch (MessageFailureException $e) {
                // server ignores the PULL ALL
                $this->handleIgnore();
                $this->handleIgnore();
                $this->sendMessage(new AckFailureMessage());
                $this->handleIgnore();
                // server success for ACK FAILURE
                $r2 = $this->handleSuccess();
                $shouldThrow = $e;
            }
        }

        if ($shouldThrow !== false) {
            throw $shouldThrow;
        }

        if ($r->isSuccess()) {
            $runResponse->onSuccess($r);
        }

        $pullResponse = new Response();

        while (!$pullResponse->isCompleted()) {
            $r = $this->unpacker->unpack();

            if ($r->isRecord()) {
                $pullResponse->onRecord($r);
            }

            if ($r->isSuccess()) {
                $pullResponse->onSuccess($r);
            }
        }

        $cypherResult = new CypherResult(Statement::create($statement, $parameters, $tag));
        $cypherResult->setFields($runResponse->getMetadata()[0]->getElements());

        foreach ($pullResponse->getRecords() as $record) {
            $cypherResult->pushRecord($record);
        }

        if (null !== $pullResponse && array_key_exists(0, $pullResponse->getMetadata())) {
            $metadata = $pullResponse->getMetadata()[0]->getElements();
            $stats = array_key_exists('stats', $metadata) ? $metadata['stats'] : array();
            $cypherResult->setStatistics($stats);
        }

        return $cypherResult;
    }

    /**
     * @throws \Exception
     */
    public function init()
    {
        $this->io->assertConnected();
        $ua = Driver::getUserAgent();
        $this->sendMessage(new InitMessage($ua, $this->credentials));
        $responseMessage = $this->receiveMessageInit();

        if ($responseMessage->getSignature() != 'SUCCESS') {
            throw new \Exception('Unable to INIT');
        }

        $this->isInitialized = true;
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
        } while ($nextChunkLength > 0);

        $rawMessage = new RawMessage($bytes);

        $message = $this->serializer->deserialize($rawMessage);

        if ($message->getSignature() === 'FAILURE') {
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

        $chunkHeader = $this->io->read(2);
        list(, $chunkSize) = unpack('n', $chunkHeader);
        $nextChunkLength = $chunkSize;

        do {
            if ($nextChunkLength) {
                $bytes .= $this->io->read($nextChunkLength);
            }

            list(, $next) = unpack('n', $this->io->read(2));
            $nextChunkLength = $next;
        } while ($nextChunkLength > 0);

        $rawMessage = new RawMessage($bytes);
        $message = $this->serializer->deserialize($rawMessage);

        if ($message->getSignature() === 'FAILURE') {
            $msg = sprintf('Neo4j Exception "%s" with code "%s"', $message->getElements()['message'], $message->getElements()['code']);
            $e = new MessageFailureException($msg);
            $e->setStatusCode($message->getElements()['code']);

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
     * {@inheritdoc}
     */
    public function close()
    {
        $this->io->close();
        $this->isInitialized = false;
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

    private function handleSuccess()
    {
        return $this->handleMessage('SUCCESS');
    }

    private function handleIgnore()
    {
        $this->handleMessage('IGNORED');
    }

    private function handleMessage($messageType)
    {
        $message = $this->unpacker->unpack();
        if ($messageType !== $message->getSignature()) {
            throw new \RuntimeException(sprintf('Expected an %s message, got %s', $messageType, $message->getSignature()));
        }

        return $message;
    }
}
