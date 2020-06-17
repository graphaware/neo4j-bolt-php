<?php


namespace PTS\Bolt\Type\Temporal;

use PTS\Bolt\PackStream\Packer;
use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Type\PackableType;

class DateTimeOffset implements DateTimeConvertible, PackableType
{
    const MARKER = Constants::MARKER_DATE_TIME_OFFSET;
    const SIGNATURE = Constants::SIGNATURE_DATE_TIME_OFFSET;

    private $epochSeconds;

    private $nanoseconds;

    private $zoneOffset;

    /**
     * DateTimeZoned constructor.
     * @param int $epochSeconds
     * @param int $nanoseconds
     * @param int $zoneOffset
     */
    public function __construct(int $epochSeconds, int $nanoseconds, int $zoneOffset)
    {
        $this->epochSeconds = $epochSeconds;
        $this->nanoseconds = $nanoseconds;
        $this->zoneOffset = $zoneOffset;
    }


    public static function fromDateTime(\DateTimeInterface $dateTime): self
    {
        return new self(
            (int)$dateTime->format('U'),
            $dateTime->format('u')*1000,
            $dateTime->getOffset()
        );
    }

    public function toDateTime(): \DateTime
    {
        $date = new \DateTime();
        $date
            ->setTimestamp($this->epochSeconds)
            ->setTimezone(new \DateTimeZone(timezone_name_from_abbr('', $this->zoneOffset, 1)));
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
            .$packer->packInteger($this->getEpochSeconds())
            .$packer->packInteger($this->getNanoseconds())
            .$packer->packInteger($this->getZoneOffset());
    }
}
