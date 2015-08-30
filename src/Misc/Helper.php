<?php

namespace GraphAware\Bolt\Misc;

class Helper
{
    public static function prettyHex($raw)
    {
        $split = str_split(bin2hex($raw), 2);

        return implode(':', $split);
    }
}