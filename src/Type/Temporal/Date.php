<?php


namespace GraphAware\Bolt\Type\Temporal;


use GraphAware\Bolt\PackStream\Packer;
use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Type\PackableType;

class Date implements DateTimeConvertible, PackableType
{
    const MARKER = Constants::MARKER_DATE;
    const SIGNATURE = Constants::SIGNATURE_DATE;


    private $daysSinceEpoch;

    /**
     * Date constructor.
     * @param int $daysSinceEpoch
     */
    public function __construct(int $daysSinceEpoch)
    {
        $this->daysSinceEpoch = $daysSinceEpoch;
    }


    static public function fromDateTime(\DateTimeInterface $dateTime): self
    {
        $epoch = (new \DateTime())->setTimestamp(0);
        $diff = $dateTime->diff($epoch);
        return new self((int)$diff->format('%a'));
    }

    public function toDateTime(): \DateTime
    {
        $epoch = (new \DateTime())->setTimestamp(0);
        $epoch->add(new \DateInterval('P'.$this->daysSinceEpoch.'D'));
        return $epoch;
    }

    /**
     * @return int
     */
    public function getDaysSinceEpoch(): int
    {
        return $this->daysSinceEpoch;
    }

    public function pack(Packer $packer): string
    {
        $str = chr(self::MARKER).chr(self::SIGNATURE);
        return $str
            .$packer->packInteger($this->getDaysSinceEpoch());
    }
}