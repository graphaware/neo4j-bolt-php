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

    const TLSMODE_REQUIRED = "REQUIRED";
    const TLSMODE_REJECTED = "REJECTED";

    private static $DEFAULT_INTERFACE = "null";
    private static $DEFAULT_TIMEOUT = 5;
    private static $DEFAULT_USER = "null";
    private static $DEFAULT_PASSWORD = "null";


    /**
     * @var array
     */
    protected $credentials;

    protected $username;

    protected $password;

    /**
     * @var string
     */
    protected $bindtoInterface;

    /**
     * @var int
     */
    protected $timeout;

    protected $tlsMode;
    /**
     * @param string $username
     * @param string $password
     */
    private function __construct($username, $password, $interface, $timeout)
    {
        $this->checkCredentials($username, $password);
        $this->bindtoInterface = $interface;
        $this->timeout = $timeout;
    }

    public static function newInstance()
    {
        return new self(self::$DEFAULT_USER, self::$DEFAULT_PASSWORD, self::$DEFAULT_INTERFACE, self::$DEFAULT_TIMEOUT);
    }

    private function checkCredentials($username, $password)
    {
        if (null !== $username && null !== $password) {
            $this->username = $username;
            $this->password = $password;
            $this->credentials = array($username, $password);
        }
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return Configuration
     */
    public function withCredentials($username, $password)
    {
        return new self($username, $password, $this->getBindtoInterface(), $this->getTimeout());
    }

    public function bindToInterface($interface)
    {
        return new self($this->getUsername(), $this->getPassword(), $interface, $this->getTimeout());
    }

    public function withTimeout($timeout)
    {
        return new self($this->getUsername(), $this->getPassword(), $this->getBindtoInterface(), $timeout);
    }

    /**
     * @return array
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @return string
     */
    public function getBindtoInterface()
    {
        return $this->bindtoInterface;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function withTLSMode($mode)
    {
        $this->tlsMode = $mode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTlsMode()
    {
        return $this->tlsMode;
    }


}
