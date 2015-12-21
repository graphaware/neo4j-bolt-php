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

use GraphAware\Common\Type\RelationshipInterface;

class UnboundRelationship implements RelationshipInterface
{
    /**
     * @var string
     */
    protected $identity;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $properties;

    /**
     * UnboundRelationship constructor.
     * @param string $identity
     * @param string $type
     * @param string array $properties
     */
    public function __construct($identity, $type, array $properties)
    {
        $this->identity = $identity;
        $this->type = $type;
        $this->properties = $properties;
    }

    /**
     * @return string
     */
    function identity()
    {
        return $this->identity;
    }

    /**
     * @return string
     */
    function type()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return bool
     */
    function hasType($type)
    {
        return $this->type === $type;
    }

}