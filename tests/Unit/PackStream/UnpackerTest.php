<?php

namespace GraphAware\Bolt\Tests\Unit\PackSream;

use GraphAware\Bolt\PackStream\BytesWalker;
use GraphAware\Bolt\PackStream\Unpacker;
use GraphAware\Bolt\Protocol\Message\RawMessage;
use Neo4j\PackStream\PackStream\Packer;

/**
 * Class UnpackerTest
 * @package GraphAware\Bolt\Tests\Unit\PackSream
 *
 * @group unit
 * @group unpack
 */
class UnpackerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \GraphAware\Bolt\PackStream\Unpacker
     */
    protected $unpacker;

    public function setUp()
    {
        $this->unpacker = new Unpacker();
    }

    public function testGetSignature()
    {
        $bytes = hex2bin("b170a0");
        $raw = new RawMessage($bytes);
        $walker = new BytesWalker($raw);
        $walker->forward(1);

        $sig = $this->unpacker->getSignature($walker);
        $this->assertEquals('SUCCESS', $sig);
    }
}