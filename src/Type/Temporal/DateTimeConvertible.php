<?php


namespace GraphAware\Bolt\Type\Temporal;


interface DateTimeConvertible
{
    /**
     * Create object from DateTime
     * @param \DateTimeInterface $dateTime
     * @return static
     */
    static public function fromDateTime(\DateTimeInterface $dateTime);

    /**
     * Create DateTime object from current temporal
     * @return \DateTime
     */
    public function toDateTime(): \DateTime;
}