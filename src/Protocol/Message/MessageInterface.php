<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\Protocol\Message;

interface MessageInterface
{
    /**
     * @return string
     */
    public function getSignature();

    /**
     * @return string
     */
    public function getMessageType();

    /**
     * @return array
     */
    public function getFields();

    /**
     * @return bool
     */
    public function isSuccess();

    /**
     * @return bool
     */
    public function isFailure();

    /*
    public function isIgnored();

    public function isRecord();
    */
}
