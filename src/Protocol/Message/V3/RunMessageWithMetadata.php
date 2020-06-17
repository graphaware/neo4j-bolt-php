<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol\Message\V3;

use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Protocol\Message\AbstractMessage;

class RunMessageWithMetadata extends AbstractMessage
{
    const MESSAGE_TYPE = 'RUN';

    /**
     * @var string
     */
    protected $statement;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var null|string
     */
    protected $tag;
    /**
     * @var array
     */
    protected $meta;

    /**`
     * @param string $statement
     * @param array $params
     * @param array $meta
     */
    public function __construct($statement, array $params = [], array $meta = [])
    {
        parent::__construct(Constants::SIGNATURE_RUN, [
            'statement' => $statement,
            'parameters' => $params,
            'metadata' => $meta
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }
}
