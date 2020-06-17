<?php


namespace GraphAware\Bolt\Type;


use GraphAware\Bolt\PackStream\Packer;

interface PackableType
{
    public function pack(Packer $packer): string;
}