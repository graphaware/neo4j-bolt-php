<?php

namespace GraphAware\Bolt\Protocol\V1;

use GraphAware\Bolt\Driver;
use GraphAware\Bolt\Misc\Helper;
use GraphAware\Bolt\Protocol\AbstractSession;
use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;
use GraphAware\Bolt\Protocol\Message\InitMessage;
use GraphAware\Bolt\Protocol\Message\PullAllMessage;
use GraphAware\Bolt\Protocol\Message\RawMessage;
use GraphAware\Bolt\Protocol\Message\RunMessage;
use GraphAware\Bolt\Protocol\Pipeline;

class Session extends AbstractSession
{
    const PROTOCOL_VERSION = 1;

    protected $isInitialized = false;

    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }

    public function run($statement, array $parameters = array(), $autoReceive = true)
    {
        $response = [];
        $messages = array(
            new RunMessage($statement, $parameters),
            new PullAllMessage()
        );

        if (!$this->isInitialized) {
            $this->init();
        }

        $this->sendMessages($messages);
        $t = microtime(true);
        if ($autoReceive) {
            foreach ($messages as $m) {
                $hasMore = true;
                while ($hasMore) {
                    $responseMessage = $this->receiveMessage();
                    if ($responseMessage->isSuccess()) {
                        $hasMore = false;
                        if ($responseMessage->hasFields()) {
                            //var_dump($responseMessage);
                        }
                    } elseif ($responseMessage->isRecord()) {
                        $response['records'][] = $responseMessage;
                    }
                }
            }
            $e = microtime(true);
            $d = $e - $t;
            var_dump($d);
            return $response;
        }

        return null;
    }

    public function init()
    {
        $ua = Driver::getUserAgent();
        $this->sendMessage(new InitMessage($ua));
        $responseMessage = $this->receiveMessage();
        if ($responseMessage->isSuccess()) {
            $this->isInitialized = true;
        } else {
            throw new \Exception('Unable to INIT');
        }
        /*
        $init = $this->packer->getMessages(Constants::SIGNATURE_INIT, array($ua));
        $message = $this->packer->getSizeMarker($init) . $init . $this->packer->getEndSignature();
        $this->io->write($message);
        */

    }

    public function runPipeline(Pipeline $pipeline)
    {

    }

    /**
     * @return \GraphAware\Bolt\Protocol\Message\AbstractMessage
     */
    public function receiveMessage()
    {
        $bytes = '';

        $nextChunkLength = 2;
        do {
            $chunkHeader = $this->io->read($nextChunkLength);
            $chunkSize = hexdec(bin2hex($chunkHeader));
            if ($chunkSize) {
                $bytes .= $this->io->read($chunkSize);
            }
            $nextChunkLength = hexdec(bin2hex($this->io->read(2)));
        } while($nextChunkLength > 0);

        $rawMessage = new RawMessage($bytes);

        return $this->serializer->deserialize($rawMessage);
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
}