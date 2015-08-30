<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\Message;

use GraphAware\Bolt\Protocol\Constants;

class InitMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'INIT';

    public function __construct($userAgent)
    {
        parent::__construct(Constants::SIGNATURE_INIT, array($userAgent));
    }

    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }
}