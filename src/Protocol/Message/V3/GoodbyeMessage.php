<?php


namespace PTS\Bolt\Protocol\Message\V3;

use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Protocol\Message\AbstractMessage;

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
