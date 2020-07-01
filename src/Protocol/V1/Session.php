<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol\V1;

use PTS\Bolt\Driver;
use OutOfBoundsException;
use PTS\Bolt\IO\AbstractIO;
use PTS\Bolt\Protocol\Pipeline;
use PTS\Bolt\Exception\IOException;
use http\Exception\RuntimeException;
use PTS\Bolt\Protocol\AbstractSession;
use GraphAware\Common\Cypher\Statement;
use PTS\Bolt\Protocol\PipelineInterface;
use PTS\Bolt\Protocol\Message\RawMessage;
use PTS\Bolt\Protocol\Message\RunMessage;
use PTS\Bolt\Protocol\Message\InitMessage;
use phpDocumentor\Reflection\Types\Boolean;
use PTS\Bolt\Result\Result as CypherResult;
use PTS\Bolt\Protocol\Message\PullAllMessage;
use GraphAware\Common\Result\ResultCollection;
use PTS\Bolt\Exception\SerializationException;
use PTS\Bolt\Protocol\Message\AbstractMessage;
use PTS\Bolt\Exception\MessageFailureException;
use RuntimeException as GlobalRuntimeException;
use PTS\Bolt\Exception\BoltOutOfBoundsException;
use PTS\Bolt\Protocol\Message\AckFailureMessage;
use PTS\Bolt\Exception\BoltInvalidArgumentException;
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

    private $remainingResponses = 0;

    /**
     * @param AbstractIO $io
     * @param EventDispatcherInterface $dispatcher
     * @param array $credentials
     * @param bool $init
     * @throws \Exception
     */
    public function __construct(
        AbstractIO $io,
        EventDispatcherInterface $dispatcher,
        array $credentials = [],
        $init = true
    ) {
        parent::__construct($io, $dispatcher);
        $this->credentials = $credentials;
        if ($init) {
            $this->init();
        }
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
    public function run($statement, array $parameters = [], $tag = null)
    {
        if (!$statement) {
            throw new BoltInvalidArgumentException("Statement cannot be null");
        }
        $messages = [
            $this->createRunMessage($statement, $parameters),
            $this->createPullAllMessage()
        ];

        $this->sendMessages($messages);
        return $this->fetchRunResult($statement, $parameters, $tag);
    }

    protected function fetchRunResult($statement, array $parameters = [], $tag = null): CypherResult
    {
        $runResponse = $this->fetchResponse();
        $pullResponse = $this->fetchResponse();

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
     * @return Response
     */
    protected function fetchResponse()
    {
        $response = new Response();
        $this->remainingResponses -= 1;
        while (!$response->isCompleted()) {
            $r = $this->unpacker->unpack();
            
            if ($r->isIgnored()) {
                $response->onIgnored($r);
            }

            if ($r->isRecord()) {
                $response->onRecord($r);
            }

            if ($r->isSuccess()) {
                $response->onSuccess($r);
            }

            if ($r->isFailure()) {
                try {
                    $response->onFailure($r);
                } catch (MessageFailureException $e) {
                    $this->handleFailure();
                    throw $e;
                }
            }
        }
        return $response;
    }

    protected function createRunMessage($statement, $prams = [])
    {
        return new RunMessage($statement, $prams);
    }

    protected function createPullAllMessage()
    {
        return new PullAllMessage();
    }

    /**
     * {@inheritdoc}
     */
    public function runPipeline(PipelineInterface $pipeline)
    {
        $resultCollection = new ResultCollection();
        $messages = $pipeline->getMessages();
        $boltMessages = [];
        // create messages
        foreach ($messages as $message) {
            $boltMessages[] = $this->createRunMessage($message->getStatement(), $message->getParameters());
            $boltMessages[] = $this->createPullAllMessage();
        }
        $this->sendMessages($boltMessages);
        // fetch responses
        foreach ($messages as $message) {
            $result = $this->fetchRunResult(
                $message->getStatement(),
                $message->getParameters(),
                $message->getTag()
            );
            $resultCollection->add($result);
        }
        return $resultCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function createPipeline($query = null, array $parameters = [], $tag = null)
    {
        return new Pipeline($this);
    }

    /**
     * @throws \Exception
     */
    public function init()
    {
        $this->io->assertConnected();
        $ua = Driver::getUserAgent();
        $this->sendMessage(new InitMessage($ua, $this->credentials));
        $response = $this->fetchResponse();

        if (!$response->isSuccess()) {
            throw new \Exception('Unable to INIT');
        }

        $this->isInitialized = true;
    }

    /**
     * @param \PTS\Bolt\Protocol\Message\AbstractMessage $message
     */
    public function sendMessage(AbstractMessage $message)
    {
        $this->sendMessages([$message]);
    }

    /**
     * @param \PTS\Bolt\Protocol\Message\AbstractMessage[] $messages
     */
    public function sendMessages(array $messages)
    {
        foreach ($messages as $message) {
            $this->serializer->serialize($message);
        }

        $this->writer->writeMessages($messages);
        $this->remainingResponses += sizeof($messages);
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

    /**
     * @return void
     */
    public function handleFailure()
    {
        while ($this->remainingResponses > 0) {
            // Ignored messages
            $this->fetchResponse();
        }
        $this->sendMessage(new AckFailureMessage());
        // Failure ack success
        $this->fetchResponse();
    }

    public function begin()
    {
        throw new \RuntimeException('Bolt protocol V1 does not support transaction messages.');
    }

    public function commit()
    {
        throw new \RuntimeException('Bolt protocol V1 does not support transaction messages.');
    }

    public function rollback()
    {
        throw new \RuntimeException('Bolt protocol V1 does not support transaction messages.');
    }
}
