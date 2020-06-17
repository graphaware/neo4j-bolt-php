<?php


namespace PTS\Bolt\Protocol\Message\V3;

use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Protocol\Message\AbstractMessage;

class RollbackMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'ROLLBACK';

    /**
     * Rollback message rollbacks a transaction
     */
    public function __construct()
    {
        parent::__construct(Constants::SIGNATURE_ROLLBACK);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }
}
