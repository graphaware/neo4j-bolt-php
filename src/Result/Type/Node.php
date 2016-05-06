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

use GraphAware\Common\Type\Node as BaseNodeInterface;

class Node extends MapAccess implements BaseNodeInterface
{
    /**
     * @var int
     */
    protected $identity;

    /**
     * @var array
     */
    protected $labels;

    /**
     * @param int   $identity
     * @param array $labels
     * @param array $properties
     */
    public function __construct($identity, array $labels = [], array $properties = [])
    {
        $this->identity = $identity;
        $this->labels = $labels;
        $this->properties = $properties;
    }

    /**
     * {@inheritdoc}
     */
    public function identity()
    {
        return $this->identity;
    }

    /**
     * {@inheritdoc}
     */
    public function labels()
    {
        return $this->labels;
    }

    /**
     * {@inheritdoc}
     */
    public function hasLabel($label)
    {
        return in_array($label, $this->labels);
    }
}
