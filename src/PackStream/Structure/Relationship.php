<?php

namespace GraphAware\Bolt\PackStream\Structure;

class Relationship extends AbstractElement
{
    /**
     * @var \GraphAware\Bolt\PackStream\Structure\TextElement
     */
    protected $identity;

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\TextElement
     */
    protected $startNode;

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\TextElement
     */
    protected $endNode;

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\TextElement
     */
    protected $type;

    /**
     * @var \GraphAware\Bolt\PackStream\Structure\Map
     */
    protected $properties;

    /**
     * @param \GraphAware\Bolt\PackStream\Structure\TextElement $identity
     * @param \GraphAware\Bolt\PackStream\Structure\TextElement $startNode
     * @param \GraphAware\Bolt\PackStream\Structure\TextElement $endNode
     * @param \GraphAware\Bolt\PackStream\Structure\TextElement $type
     * @param \GraphAware\Bolt\PackStream\Structure\Map $properties
     */
    public function __construct(TextElement $identity, TextElement $startNode, TextElement $endNode, TextElement $type, Map $properties)
    {
        $this->identity = $identity;
        $this->startNode = $startNode;
        $this->endNode = $endNode;
        $this->type = $type;
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


}