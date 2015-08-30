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

use GraphAware\Bolt\PackStream\Structure\ListCollection;
use GraphAware\Bolt\Protocol\Constants;

class RecordMessage extends AbstractMessage
{
    const MESSAGE_TYPE = 'RECORD';

    protected $values;

    public function __construct(ListCollection $list)
    {
        parent::__construct(Constants::SIGNATURE_RECORD);
        $this->values = $list;
    }

    public function getMessageType()
    {
        return self::MESSAGE_TYPE;
    }

    public function getValues()
    {
        return $this->values;
    }
}