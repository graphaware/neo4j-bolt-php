<?php


namespace GraphAware\Bolt\Type;


class Point3D extends Point2D
{
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


}