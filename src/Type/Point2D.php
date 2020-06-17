<?php


namespace PTS\Bolt\Type;

use PTS\Bolt\PackStream\Packer;
use PTS\Bolt\Protocol\Constants;

class Point2D implements PackableType
{
    const SIGNATURE = Constants::SIGNATURE_POINT2D;
    const MARKER = Constants::MARKER_POINT2D;

    protected $x;

    protected $y;

    protected $srid;

    /**
     * Point2D constructor.
     * @param $x
     * @param $y
     * @param $srid
     */
    public function __construct($x, $y, $srid = 7203)
    {
        $this->x = $x;
        $this->y = $y;
        $this->srid = $srid;
    }


    /**
     * @return mixed
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return mixed
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @return mixed
     */
    public function getSrid()
    {
        return $this->srid;
    }


    /**
     * @param Packer $v
     *
     * @return string
     */
    public function pack(Packer $packer): string
    {
        $str = chr(self::MARKER) . chr(self::SIGNATURE);
        return $str
            . $packer->packInteger($this->getSrid())
            . $packer->packFloat($this->getX())
            . $packer->packFloat($this->getY());
    }
}
