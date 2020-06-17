<?php


namespace PTS\Bolt\Type\Temporal;

use PTS\Bolt\PackStream\Packer;
use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Type\PackableType;

class Duration implements PackableType
{
    const MARKER = Constants::MARKER_DURATION;
    const SIGNATURE = Constants::SIGNATURE_DURATION;

    private $months;

    private $days;

    private $seconds;

    private $nanoSeconds;

    /**
     * Duration constructor.
     * @param $months
     * @param $days
     * @param $seconds
     * @param $nanoSeconds
     */
    public function __construct($months, $days, $seconds, $nanoSeconds)
    {
        $this->months = $months;
        $this->days = $days;
        $this->seconds = $seconds;
        $this->nanoSeconds = $nanoSeconds;
    }

    /**
     * @param \DateInterval $interval
     * @return static
     */
    public static function fromDateInterval(\DateInterval $interval): self
    {
        // Years are turned into months, minutes and hours are turned into seconds
        return new self(
            (int) $interval->format('%m') + (int) $interval->format('%y')*12,
            (int) $interval->format('%d'),
            (int) $interval->format('%s')
                + (int) $interval->format('%i')*60
                + (int) $interval->format('%h')*3600,
            (int)$interval->format('%f') * 1000
        );
    }

    public function toDateInterval(): \DateInterval
    {
        return new \DateInterval(sprintf(
            'P%dM%dDT%dS',
            $this->months,
            $this->days,
            $this->seconds
        ));
    }

    /**
     * @return mixed
     */
    public function getMonths()
    {
        return $this->months;
    }

    /**
     * @return mixed
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @return mixed
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @return mixed
     */
    public function getNanoSeconds()
    {
        return $this->nanoSeconds;
    }


    public function pack(Packer $packer): string
    {
        $str = chr(self::MARKER).chr(self::SIGNATURE);
        return $str
            .$packer->packInteger($this->getMonths())
            .$packer->packInteger($this->getDays())
            .$packer->packInteger($this->getSeconds())
            .$packer->packInteger($this->getNanoSeconds());
    }
}
