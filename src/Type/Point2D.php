<?php


namespace GraphAware\Bolt\Type;


class Point2D
{
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



}