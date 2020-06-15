<?php

namespace GraphAware\Bolt\Tests\Integration\Packing;

use GraphAware\Bolt\Tests\IntegrationTestCase;
use GraphAware\Bolt\Type\Point2D;
use GraphAware\Bolt\Type\Point3D;

/**
 * @group packing
 * @group integration
 * @group floats
 */
class PackingPointsIntegrationTest extends IntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->emptyDB();
    }

    public function testPackingPoint2D()
    {
        $session = $this->getSession();
        $point = new Point2D(5421394.5693251, 1.9287);
        $result = $session->run('CREATE (n:Point) SET n.prop = $x RETURN n.prop as x', ['x' => $point]);
        /**
         * @var \GraphAware\Bolt\Result\Type\Point2D $pointOut
         */
        $pointOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(\GraphAware\Bolt\Result\Type\Point2D::class, $pointOut);
        $this->assertEquals($point->getSrid(), $pointOut->getSrid());
        $this->assertEquals($point->getX(), $pointOut->getX());
        $this->assertEquals($point->getY(), $pointOut->getY());
    }

    public function testPackingPoint3D()
    {
        $session = $this->getSession();
        $point = new Point3D(1234.56543, 5421394.5693251, 1.9287);
        $result = $session->run('CREATE (n:Point3d) SET n.prop = $x RETURN n.prop as x', ['x' => $point]);
        /**
         * @var \GraphAware\Bolt\Result\Type\Point3D $pointOut
         */
        $pointOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(\GraphAware\Bolt\Result\Type\Point3D::class, $pointOut);
        $this->assertEquals($point->getSrid(), $pointOut->getSrid());
        $this->assertEquals($point->getX(), $pointOut->getX());
        $this->assertEquals($point->getY(), $pointOut->getY());
        $this->assertEquals($point->getZ(), $pointOut->getZ());
    }


}
