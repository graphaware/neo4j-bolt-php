<?php


namespace GraphAware\Bolt\Protocol\Message\V3;

use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;

class CommitMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'COMMIT';

    /**
     * Begin message starts transaction
     */
    public function __construct()
    {
        parent::__construct(Constants::SIGNATURE_COMMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

}