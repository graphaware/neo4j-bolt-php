<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt;

use GraphAware\Common\Driver\ConfigInterface;

class Configuration implements ConfigInterface
{
    /**
     * @var array
     */
    protected $credentials;

    /**
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password)
    {
        $this->credentials = array($username, $password);
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return Configuration
     */
    public static function withCredentials($username, $password)
    {
        return new self($username, $password);
    }

    /**
     * @return array
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
}
