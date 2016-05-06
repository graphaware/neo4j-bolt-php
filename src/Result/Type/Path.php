<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Result\Type;

use GraphAware\Common\Type\Node as NodeInterface;
use GraphAware\Common\Type\Path as BasePathInterface;
use GraphAware\Common\Type\Relationship as RelationshipInterface;

class Path implements BasePathInterface
{
    /**
     * @var NodeInterface[]
     */
    protected $nodes;

    /**
     * @var UnboundRelationship[]
     */
    protected $relationships;

    /**
     * @var \int[]
     */
    protected $sequence;

    /**
     * @param NodeInterface[]       $nodes
     * @param UnboundRelationship[] $relationships
     * @param int[]                 $sequence
     */
    public function __construct(array $nodes, array $relationships, array $sequence)
    {
        $this->nodes = $nodes;
        $this->relationships = $relationships;
        $this->sequence = $sequence;
    }

    /**
     * @return NodeInterface
     */
    public function start()
    {
        return $this->nodes[0];
    }

    /**
     * @return Node
     */
    public function end()
    {
        return $this->nodes[count($this->nodes) - 1];
    }

    /**
     * {@inheritdoc}
     */
    public function length()
    {
        return count($this->relationships);
    }

    /**
     * {@inheritdoc}
     */
    public function containsNode(NodeInterface $node)
    {
        return in_array($node, $this->nodes);
    }

    /**
     * {@inheritdoc}
     */
    public function containsRelationship(RelationshipInterface $relationship)
    {
        return in_array($relationship, $this->relationships);
    }

    /**
     * @return Node[]
     */
    public function nodes()
    {
        return $this->nodes;
    }

    /**
     * @return UnboundRelationship[]
     */
    public function relationships()
    {
        return $this->relationships;
    }
}
