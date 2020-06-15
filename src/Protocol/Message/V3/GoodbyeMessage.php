<?php


namespace GraphAware\Bolt\Protocol\Message\V3;

use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;

class GoodbyeMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'GOODBYE';

    /**
     * Begin message starts transaction
     * @param array $metaData
     */
    public function __construct()
    {
        parent::__construct(Constants::SIGNATURE_GOODBYE);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

}