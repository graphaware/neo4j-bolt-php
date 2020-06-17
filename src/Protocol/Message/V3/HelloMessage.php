<?php


namespace PTS\Bolt\Protocol\Message\V3;

use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Protocol\Message\AbstractMessage;

class HelloMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'INIT';

    /**
     * Hello message combines auth and user agent into metadata field
     * @param string $userAgent
     * @param array  $credentials
     */
    public function __construct($userAgent, array $credentials)
    {
        $metaData = [];

        if (isset($credentials[1]) && null !== $credentials[1]) {
            $metaData = [
                'scheme' => 'basic',
                'principal' => $credentials[0],
                'credentials' => $credentials[1],
                'user_agent' => $userAgent
            ];
        }

        parent::__construct(Constants::SIGNATURE_HELLO, ['metadata' => $metaData]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }
}
