<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol\Message\V4;

use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Protocol\Message\AbstractMessage;

class PullMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'PULL';

    public function __construct(array $metaData = [])
    {
        parent::__construct(Constants::SIGNATURE_PULL, ['metadata' => $metaData]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }
}
