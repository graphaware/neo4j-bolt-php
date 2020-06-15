<?php


namespace GraphAware\Bolt\Protocol\Message\V3;


use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;

class BeginMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'BEGIN';

    /**
     * Begin message starts transaction
     * @param array $metaData
     */
    public function __construct(array $metaData = [])
    {
        parent::__construct(Constants::SIGNATURE_BEGIN, ['metadata' => $metaData]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

}