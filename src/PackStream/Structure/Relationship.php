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

class Relationship extends AbstractElement
{
    /**
     * @var
     */
    protected $identity;

    /**
     * @var
     */
    protected $startNode;

    /**
     * @var
     */
    protected $endNode;

    /**
     * @var
     */
    protected $type;

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\Map
     */
    protected $properties;

    /**
     * @param \GraphAware\Bolt\PackStream\Structure\SimpleElement $identity
     * @param \GraphAware\Bolt\PackStream\Structure\SimpleElement $startNode
     * @param \GraphAware\Bolt\PackStream\Structure\SimpleElement $endNode
     * @param \GraphAware\Bolt\PackStream\Structure\SimpleElement $type
     * @param \GraphAware\Bolt\PackStream\Structure\Map $properties
     */
    public function __construct(SimpleElement $identity, SimpleElement $startNode, SimpleElement $endNode, SimpleElement $type, Map $properties)
    {
        $this->identity = $identity->getValue();
        $this->startNode = $startNode->getValue();
        $this->endNode = $endNode->getValue();
        $this->type = $type->getValue();
        $this->properties = $properties;
    }

    /**
     * @return TextElement
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @return TextElement
     */
    public function getStartNode()
    {
        return $this->startNode;
    }

    /**
     * @return TextElement
     */
    public function getEndNode()
    {
        return $this->endNode;
    }

    /**
     * @return TextElement
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return Map
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @return $this
     */
    public function getValue()
    {
        return $this;
    }
}