<?php


namespace PTS\Bolt\Type;

use PTS\Bolt\PackStream\Packer;

interface PackableType
{
    public function pack(Packer $packer): string;
}
