<?php

namespace GraphAware\Bolt\Protocol\V1;

use GraphAware\Bolt\Driver;
use GraphAware\Bolt\IO\AbstractIO;
use GraphAware\Bolt\Misc\Helper;
use GraphAware\Bolt\PackStream\BytesWalker;
use GraphAware\Bolt\PackStream\Packer;
use GraphAware\Bolt\PackStream\Unpacker;
use GraphAware\Bolt\Protocol\Constants;

class Connection
{
    protected $stream;

    protected $packer;

    protected $unpacker;

    protected $messages = [];

    /**
     * @var \GraphAware\Bolt\Protocol\V1\Response[]
     */
    protected $responses = [];

    public function __construct(AbstractIO $stream)
    {
        $this->stream = $stream;
        $this->packer = new Packer();
        $this->unpacker = new Unpacker();
        $response = new Response();
        $response->registerCallback(Response::ON_FAILURE, function() {
            throw new \Exception('Exception during INIT');
        });
        $this->add(Constants::SIGNATURE_INIT, array(Driver::getUserAgent()), $response);
        $this->send();
        while (!$response->isCompleted()) {
            $this->fetchNext();
        }
    }

    public function add($signature, array $fields = array(), Response $response)
    {
        $b = '';
        $b .= $this->packer->packStructureHeader(count($fields), $signature);
        foreach ($fields as $field) {
            $b .= $this->packer->pack($field);
        }

        $this->messages[] = $b;
        $this->responses[] = $response;
    }

    public function send()
    {
        $this->flush();
    }

    public function flush()
    {
        echo 'number of messages ' . count($this->messages) . PHP_EOL;
        $raw = '';
        foreach ($this->messages as $message) {
            $chunks = str_split($message, 8192);
            foreach ($chunks as $chunk) {
                $raw .= $this->packer->getSizeMarker($chunk);
                $raw .= $chunk;
                array_shift($this->messages);
            }
            $raw .= $this->packer->getEndSignature();
        }
        $this->stream->write($raw);
    }

    public function fetchNext()
    {
        $b = '';
        do {
            list(, $l) = unpack('n', $this->stream->read(2));
            $b .= $this->stream->read($l);
        } while ($l > 0);

        $unpack = $this->unpacker->unpackElement(new BytesWalker($b));
        $response = $this->responses[0];
        if ($unpack->isSuccess()) {
            $response->setCompleted();
            array_shift($this->responses);
            var_dump(count($this->responses));
        }
        //print_r($unpack);
//        exit();
    }

    public function close()
    {
        $this->stream->close();
    }
}