<?php


namespace GraphAware\Bolt\Type\Temporal;


use GraphAware\Bolt\PackStream\Packer;
use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Type\PackableType;

class LocalDateTime implements DateTimeConvertible, PackableType
{
    const MARKER = Constants::MARKER_LOCAL_DATE_TIME;
    const SIGNATURE = Constants::SIGNATURE_LOCAL_DATE_TIME;

    private $epochSeconds;

    private $nanoseconds;

    /**
     * LocalDateTime constructor.
     * @param $epochSeconds
     * @param $nanoseconds
     */
    public function __construct($epochSeconds, $nanoseconds)
    {
        $this->epochSeconds = $epochSeconds;
        $this->nanoseconds = $nanoseconds;
    }


    static public function fromDateTime(\DateTimeInterface $dateTime)
    {
        return new self(
            (int)$dateTime->format('U'),
            $dateTime->format('u')*1000
        );
    }

    public function toDateTime(): \DateTime
    {
        return new \DateTime('@'.$this->epochSeconds);
    }

    /**
     * @return mixed
     */
    public function getEpochSeconds()
    {
        return $this->epochSeconds;
    }

    /**
     * @return mixed
     */
    public function getNanoseconds()
    {
        return $this->nanoseconds;
    }


    public function pack(Packer $packer): string
    {
        $str = chr(self::MARKER).chr(self::SIGNATURE);
        return $str
            .$packer->packInteger($this->getEpochSeconds())
            .$packer->packInteger($this->getNanoseconds());
    }
}