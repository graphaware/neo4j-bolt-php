<?php


namespace PTS\Bolt\Type;

use PTS\Bolt\PackStream\Packer;
use PTS\Bolt\Protocol\Constants;

class Point3D extends Point2D
{
    const SIGNATURE = Constants::SIGNATURE_POINT3D;
    const MARKER = Constants::MARKER_POINT3D;

    protected $z;

    /**
     * Point3D constructor.
     * @param $x
     * @param $y
     * @param $z
     * @param int $srid
     */
    public function __construct($x, $y, $z, $srid = 9157)
    {
        $this->z = $z;
        parent::__construct($x, $y, $srid);
    }

    /**
     * @return mixed
     */
    public function getZ()
    {
        return $this->z;
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
            . $packer->packFloat($this->getY())
            . $packer->packFloat($this->getZ());
    }
}
