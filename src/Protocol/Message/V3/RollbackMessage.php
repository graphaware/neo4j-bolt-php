<?php


namespace GraphAware\Bolt\Protocol\Message\V3;

use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;

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