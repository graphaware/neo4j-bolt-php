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

class Relationship implements RelationshipInterface
{
    /**
     * @var int
     */
    protected $identity;

    /**
     * @var int
     */
    protected $startNodeIdentity;

    /**
     * @var int
     */
    protected $endNodeIdentity;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $properties;

    /**
     * Relationship constructor.
     * @param int $identity
     * @param int $startNodeIdentity
     * @param int $endNodeIdentity
     * @param string $type
     * @param array $properties
     */
    public function __construct($identity, $startNodeIdentity, $endNodeIdentity, $type, array $properties = array())
    {
        $this->identity = $identity;
        $this->startNodeIdentity = $startNodeIdentity;
        $this->endNodeIdentity = $endNodeIdentity;
        $this->type = $type;
        $this->properties = $properties;
    }

    /**
     * @return int
     */
    public function identity()
    {
        return $this->identity;
    }

    /**
     * @return int
     */
    public function startNodeIdentity()
    {
        return $this->startNodeIdentity;
    }

    /**
     * @return int
     */
    public function endNodeIdentity()
    {
        return $this->endNodeIdentity;
    }

    /**
     * @return string
     */
    public function type()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function hasType($type)
    {
        return $this->type === $type;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function value($key)
    {
        return $this->properties[$key];
    }

    /**
     * @return array
     */
    public function keys()
    {
        return array_keys($this->properties);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key)
    {
        return $this->value($key);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->properties);
    }

    /**
     * @return array
     */
    public function values()
    {
        return $this->properties;
    }

    /**
     * @return array
     */
    public function asArray()
    {
        return $this->properties;
    }

}