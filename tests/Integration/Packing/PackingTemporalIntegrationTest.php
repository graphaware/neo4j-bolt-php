<?php

namespace PTS\Bolt\Tests\Integration\Packing;

use PTS\Bolt\Tests\IntegrationTestCase;
use PTS\Bolt\Type\Point2D;
use PTS\Bolt\Type\Point3D;
use PTS\Bolt\Type\Temporal\Date;
use PTS\Bolt\Type\Temporal\DateTimeOffset;
use PTS\Bolt\Type\Temporal\DateTimeZoned;
use PTS\Bolt\Type\Temporal\Duration;
use PTS\Bolt\Type\Temporal\LocalDateTime;
use PTS\Bolt\Type\Temporal\LocalTime;
use PTS\Bolt\Type\Temporal\Time;

/**
 * @group packing
 * @group integration
 * @group temporal
 * @group V2+
 */
class PackingTemporalIntegrationTest extends IntegrationTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->emptyDB();
        if($this->driver->getAgreedVersion() < 2){
            $this->markTestSkipped('Temporal structures require bolt V2+');
        }
    }

    public function testPackingDateTimeZoned()
    {
        $session = $this->getSession();
        $date = DateTimeZoned::fromDateTime(new \DateTime('2000-01-01 01:00'));
        $result = $session->run('CREATE (n:Date) SET n.prop = $x RETURN n.prop as x', ['x' => $date]);
        /**
         * @var DateTimeZoned $dateOut
         */
        $dateOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(DateTimeZoned::class, $dateOut);
        $this->assertEquals($date->getNanoseconds(), $dateOut->getNanoseconds());
        $this->assertEquals($date->getEpochSeconds(), $dateOut->getEpochSeconds());
        $this->assertEquals($date->getZoneId(), $dateOut->getZoneId());
    }

    public function testPackingNativeDateTime()
    {
        $session = $this->getSession();
        $date = new \DateTime('2000-01-01 01:00');
        $result = $session->run('CREATE (n:Date) SET n.prop = $x RETURN n.prop as x', ['x' => $date]);
        /**
         * @var DateTimeZoned $dateOut
         */
        $dateOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(DateTimeZoned::class, $dateOut);
    }

    public function testPackingDateTimeOffset()
    {
        $session = $this->getSession();
        $date = DateTimeOffset::fromDateTime(new \DateTime('2000-01-01 01:00'));
        $result = $session->run('CREATE (n:Date) SET n.prop = $x RETURN n.prop as x', ['x' => $date]);
        /**
         * @var DateTimeOffset $dateOut
         */
        $dateOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(DateTimeOffset::class, $dateOut);
        $this->assertEquals($date->getNanoseconds(), $dateOut->getNanoseconds());
        $this->assertEquals($date->getEpochSeconds(), $dateOut->getEpochSeconds());
        $this->assertEquals($date->getZoneOffset(), $dateOut->getZoneOffset());
    }

    public function testPackingDate()
    {
        $session = $this->getSession();
        $date = Date::fromDateTime(new \DateTime('2000-01-01'));
        $result = $session->run('CREATE (n:Date) SET n.prop = $x RETURN n.prop as x', ['x' => $date]);
        /**
         * @var Date $dateOut
         */
        $dateOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(Date::class, $dateOut);
        $this->assertEquals($date->getDaysSinceEpoch(), $dateOut->getDaysSinceEpoch());
    }

    public function testPackingLocalDateTime()
    {
        $session = $this->getSession();
        $date = LocalDateTime::fromDateTime(new \DateTime('2000-01-01 01:00', new \DateTimeZone('EST')));
        $result = $session->run('CREATE (n:Date) SET n.prop = $x RETURN n.prop as x', ['x' => $date]);
        /**
         * @var LocalDateTime $dateOut
         */
        $dateOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(LocalDateTime::class, $dateOut);
        $this->assertEquals($date->getNanoseconds(), $dateOut->getNanoseconds());
        $this->assertEquals($date->getEpochSeconds(), $dateOut->getEpochSeconds());
    }

    public function testPackingLocalTime()
    {
        $session = $this->getSession();
        $time = LocalTime::fromDateTime(new \DateTime('01:00:00'));
        $result = $session->run('CREATE (n:Time) SET n.prop = $x RETURN n.prop as x', ['x' => $time]);
        /**
         * @var LocalTime $timeOut
         */
        $timeOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(LocalTime::class, $timeOut);
        $this->assertEquals($time->getNanoSecondsSinceMidnight(), $timeOut->getNanoSecondsSinceMidnight());
    }

    public function testPackingTime()
    {
        $session = $this->getSession();
        $time = Time::fromDateTime(new \DateTime('01:00:00', new \DateTimeZone('EST')));
        $result = $session->run('CREATE (n:Time) SET n.prop = $x RETURN n.prop as x', ['x' => $time]);
        /**
         * @var Time $timeOut
         */
        $timeOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(Time::class, $timeOut);
        $this->assertEquals($time->getNanoSecondsSinceMidnight(), $timeOut->getNanoSecondsSinceMidnight());
        $this->assertEquals($time->getZoneOffset(), $timeOut->getZoneOffset());
    }

    public function testPackingDuration()
    {
        $session = $this->getSession();
        $duration = Duration::fromDateInterval(new \DateInterval('P1Y1M1DT1H1M1S'));
        $result = $session->run('CREATE (n:Duration) SET n.prop = $x RETURN n.prop as x', ['x' => $duration]);
        /**
         * @var Duration $durationOut
         */
        $durationOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(Duration::class, $durationOut);
        $this->assertEquals($duration->getMonths(), $durationOut->getMonths());
        $this->assertEquals($duration->getDays(), $durationOut->getDays());
        $this->assertEquals($duration->getSeconds(), $durationOut->getSeconds());
        $this->assertEquals($duration->getNanoSeconds(), $durationOut->getNanoSeconds());
    }

    public function testPackingDateInterval()
    {
        $session = $this->getSession();
        $duration = new \DateInterval('P1Y1M1DT1H1M1S');
        $result = $session->run('CREATE (n:Duration) SET n.prop = $x RETURN n.prop as x', ['x' => $duration]);
        /**
         * @var Duration $durationOut
         */
        $durationOut = $result->getRecord()->value('x');
        $this->assertInstanceOf(Duration::class, $durationOut);
    }

    public function testUnpackingDateInterval()
    {
        $session = $this->getSession();
        $result = $session->run('RETURN duration({weeks: 1});');
        /**
         * @var Duration $durationOut
         */
        $durationOut = $result->getRecord()->getByIndex(0);
        $this->assertInstanceOf(Duration::class, $durationOut);
        $this->assertEquals(7, $durationOut->getDays());
    }

}
