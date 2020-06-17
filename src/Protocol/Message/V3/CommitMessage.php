<?php


namespace PTS\Bolt\Protocol\Message\V3;

use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Protocol\Message\AbstractMessage;

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