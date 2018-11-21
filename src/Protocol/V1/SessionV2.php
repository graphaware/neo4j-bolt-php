<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\V1;

use GraphAware\Bolt\Driver;
use GraphAware\Bolt\Exception\BoltInvalidArgumentException;
use GraphAware\Bolt\IO\AbstractIO;
use GraphAware\Bolt\Protocol\AbstractSession;
use GraphAware\Bolt\Protocol\Message\AbstractMessage;
use GraphAware\Bolt\Protocol\Message\AckFailureMessage;
use GraphAware\Bolt\Protocol\Message\InitMessage;
use GraphAware\Bolt\Protocol\Message\PullAllMessage;
use GraphAware\Bolt\Protocol\Message\RawMessage;
use GraphAware\Bolt\Protocol\Message\RunMessage;
use GraphAware\Bolt\Protocol\Pipeline;
use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Bolt\Result\Result as CypherResult;
use GraphAware\Common\Cypher\Statement;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SessionV2 extends Session
{
    const PROTOCOL_VERSION = 2;

    /**
     * {@inheritdoc}
     */
    public static function getProtocolVersion()
    {
        return self::PROTOCOL_VERSION;
    }
}
