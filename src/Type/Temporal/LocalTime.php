<?php


namespace GraphAware\Bolt\Type\Temporal;


use GraphAware\Bolt\PackStream\Packer;
use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Type\PackableType;

class LocalTime implements DateTimeConvertible, PackableType
{
    const MARKER = Constants::MARKER_LOCAL_TIME;
    const SIGNATURE = Constants::SIGNATURE_LOCAL_TIME;

    private $nanoSecondsSinceMidnight;

    /**
     * Time constructor.
     * @param int $nanoSecondsSinceMidnight
     */
    public function __construct(int $nanoSecondsSinceMidnight)
    {
        $this->nanoSecondsSinceMidnight = $nanoSecondsSinceMidnight;
    }

    static public function fromDateTime(\DateTimeInterface $dateTime)
    {
        $midnight = clone($dateTime)->modify('midnight');
        $nano = ($dateTime->getTimestamp() - $midnight->getTimestamp())*1000000000;
        return new self($nano);
    }

    public function toDateTime(): \DateTime
    {
        $date = (new \DateTime('today midnight'));
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


    public function pack(Packer $packer): string
    {
        $str = chr(self::MARKER).chr(self::SIGNATURE);
        return $str
            .$packer->packInteger($this->getNanoSecondsSinceMidnight());
    }
}