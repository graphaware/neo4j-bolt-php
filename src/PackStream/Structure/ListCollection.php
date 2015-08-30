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

use Doctrine\Common\Collections\ArrayCollection;

class ListCollection extends AbstractElement
{
    protected $elements;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function add(AbstractElement $element)
    {
        $this->elements->add($element);
    }

    public function getElements()
    {
        return $this->elements;
    }
}