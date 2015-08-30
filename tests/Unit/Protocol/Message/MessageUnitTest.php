<?php

namespace GraphAware\Bolt\Tests\Protocol\Message;

use GraphAware\Bolt\Protocol\Message\RawMessage;

/**
 * Class MessageUnitTest
 * @package GraphAware\Bolt\Tests\Protocol\Message
 *
 * @group message
 * @group unit
 */
class MessageUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testRawMessageIsSuccess()
    {
        $binary = 0x70;
        $msg = new RawMessage($binary);
        $this->assertTrue($msg->isSuccess());
    }
}