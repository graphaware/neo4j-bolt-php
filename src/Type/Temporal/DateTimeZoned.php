<?php


namespace GraphAware\Bolt\Type\Temporal;


use GraphAware\Bolt\PackStream\Packer;
use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Type\PackableType;

class DateTimeZoned implements DateTimeConvertible, PackableType
{
    const MARKER = Constants::MARKER_DATE_TIME_ZONED;
    const SIGNATURE = Constants::SIGNATURE_DATE_TIME_ZONED;

    private $epochSeconds;

    private $nanoseconds;

    private $zoneId;

    /**
     * DateTimeZoned constructor.
     * @param $epochSeconds
     * @param $nanoseconds
     * @param $zoneId
     */
    public function __construct(int $epochSeconds, int $nanoseconds, string $zoneId)
    {
        $this->epochSeconds = $epochSeconds;
        $this->nanoseconds = $nanoseconds;
        $this->zoneId = $zoneId;
    }


    static public function fromDateTime(\DateTimeInterface $dateTime): self
    {
        return new self(
            (int)$dateTime->format('U'),
            $dateTime->format('u')*1000,
            $dateTime->getTimezone()->getName()
        );
    }

    public function toDateTime(): \DateTime
    {
        $date = new \DateTime();
        $date
            ->setTimestamp($this->epochSeconds)
            ->setTimezone(new \DateTimeZone($this->zoneId));
        return $date;
    }

    /**
     * @return int
     */
    public function getEpochSeconds(): int
    {
        return $this->epochSeconds;
    }

    /**
     * @return int
     */
    public function getNanoseconds(): int
    {
        return $this->nanoseconds;
    }

    /**
     * @return string
     */
    public function getZoneId(): string
    {
        return $this->zoneId;
    }

    public function pack(Packer $packer): string
    {
        $str = chr(self::MARKER).chr(self::SIGNATURE);
        return $str
            .$packer->packInteger($this->getEpochSeconds())
            .$packer->packInteger($this->getNanoseconds())
            .$packer->packText($this->getZoneId());
    }


}