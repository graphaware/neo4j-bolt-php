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
use GraphAware\Common\Connection\Configuration as BaseConfiguration;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class Configuration extends BaseConfiguration implements ConfigInterface
{
    const TLSMODE_REQUIRED = "REQUIRED";
    const TLSMODE_REJECTED = "REJECTED";

    /**
     * @var array
     * @deprecated
     */
    protected $credentials;

    /**
     * @deprecated
     */
    protected $username;

    /**
     * @deprecated
     */
    protected $password;

    /**
     * @var string
     * @deprecated
     */
    protected $bindtoInterface;

    /**
     * @var int
     * @deprecated
     */
    protected $timeout;

    /**
     * @deprecated
     */
    protected $tlsMode;

    /**
     * Create a new configuration with default values.
     *
     * @return self
     */
    public static function create()
    {
        return new self([
            'user' => 'null',
            'password' => 'null',
            'bind_to_interface' => 'null',
            'timeout' => 5,
            'credentials' => ['null', 'null'],
        ]);
    }

    /**
     * @return Configuration
     *
     * @deprecated Will be removed in 2.0. Use Configuration::create
     */
    public static function newInstance()
    {
        return self::create();
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return Configuration
     */
    public function withCredentials($username, $password)
    {
        if (null === $username || null === $password) {
            // No change if credentials or null
            return $this;
        }

        return $this->setValue('username', $username)
            ->setValue('password', $password)
            ->setValue('credentials', [$username, $password]);
    }

    /**
     * @param string $interface
     *
     * @return Configuration
     */
    public function bindToInterface($interface)
    {
        return $this->setValue('bind_to_interface', $interface);
    }

    /**
     * @param int $timeout
     *
     * @return Configuration
     */
    public function withTimeout($timeout)
    {
        return $this->setValue('timeout', $timeout);
    }

    /**
     * @return array
     *
     * @deprecated Will be removed in 2.0. Use Configuration::getValue('credentials').
     */
    public function getCredentials()
    {
        return $this->getValue('credentials');
    }

    /**
     * @return string
     *
     * @deprecated Will be removed in 2.0. Use Configuration::getValue('bind_to_interface').
     */
    public function getBindtoInterface()
    {
        return $this->getValue('bind_to_interface');
    }

    /**
     * @return int
     *
     * @deprecated Will be removed in 2.0. Use Configuration::getValue('timeout').
     */
    public function getTimeout()
    {
        return $this->getValue('timeout');
    }

    /**
     * @return mixed
     *
     * @deprecated Will be removed in 2.0. Use Configuration::getValue('username').
     */
    public function getUsername()
    {
        return $this->getValue('username');
    }

    /**
     * @return mixed
     *
     * @deprecated Will be removed in 2.0. Use Configuration::getValue('password').
     */
    public function getPassword()
    {
        return $this->getValue('password');
    }

    /**
     * @param $mode
     *
     * @return Configuration
     */
    public function withTLSMode($mode)
    {
        return $this->setValue('tls_mode', $mode);
    }

    /**
     * @return mixed
     *
     * @deprecated Will be removed in 2.0. Use Configuration::getValue('tls_mode').
     */
    public function getTlsMode()
    {
        return $this->getValue('tls_mode');
    }
}
