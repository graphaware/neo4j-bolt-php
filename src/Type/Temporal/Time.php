<?php


namespace GraphAware\Bolt\Type\Temporal;


use GraphAware\Bolt\PackStream\Packer;
use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Type\PackableType;

class Time implements DateTimeConvertible, PackableType
{
    const MARKER = Constants::MARKER_TIME;
    const SIGNATURE = Constants::SIGNATURE_TIME;

    private $nanoSecondsSinceMidnight;

    /**
     * Zone offset in seconds
     * @var int
     */
    private $zoneOffset;

    /**
     * Time constructor.
     * @param int $nanoSecondsSinceMidnight
     * @param int $zoneOffset
     */
    public function __construct(int $nanoSecondsSinceMidnight, int $zoneOffset)
    {
        $this->nanoSecondsSinceMidnight = $nanoSecondsSinceMidnight;
        $this->zoneOffset = $zoneOffset;
    }


    static public function fromDateTime(\DateTimeInterface $dateTime)
    {
        $midnight = (clone $dateTime)->modify('midnight');
        $nano = ($dateTime->getTimestamp() - $midnight->getTimestamp())*1000000000;
        return new self($nano, $dateTime->getOffset());
    }

    public function toDateTime(): \DateTime
    {
        $date = new \DateTime(
            'today midnight',
            new \DateTimeZone(timezone_name_from_abbr('', $this->zoneOffset, 1))
        );
        $seconds = $this->nanoSecondsSinceMidnight / 1000000000;
        $date->modify("+$seconds seconds");
        return $date;
    }

    /**
     * @return int
     */
    public function getNanoSecondsSinceMidnight(): int
    {
        return $this->nanoSecondsSinceMidnight;
    }

    /**
     * @return int
     */
    public function getZoneOffset(): int
    {
        return $this->zoneOffset;
    }


    public function pack(Packer $packer): string
    {
        $str = chr(self::MARKER).chr(self::SIGNATURE);
        return $str
            .$packer->packInteger($this->getNanoSecondsSinceMidnight())
            .$packer->packInteger($this->getZoneOffset());
    }
}