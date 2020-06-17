<?php

namespace PTS\Bolt\Tests\Protocol\Message;

use PTS\Bolt\Protocol\Message\SuccessMessage;
use PTS\Bolt\Protocol\Message\AbstractMessage;

/**
 * Class MessageUnitTest
 * @package PTS\Bolt\Tests\Protocol\Message
 *
 * @group message
 * @group unit
 */
class MessageUnitTest extends \PHPUnit_Framework_TestCase
{
    public function testSuccessMessageWithoutFields()
    {
        $message = new SuccessMessage([]);
        $this->assertInstanceOf(AbstractMessage::class, $message);
    }
}
