<?php


namespace PTS\Bolt\Protocol\Message\V3;


use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Protocol\Message\AbstractMessage;

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