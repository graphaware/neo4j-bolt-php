<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\PackStream\Structure;

class SimpleElement extends AbstractElement
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        if (null === $this->value) {
            return (string) 'null';
        }

        if (true === $this->value) {
            return 'true';
        }

        if (false === $this->value) {
            return 'false';
        }
        return (string) $this->getValue();
    }
}