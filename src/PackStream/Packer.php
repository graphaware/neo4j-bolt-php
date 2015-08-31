<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\PackStream;

use GraphAware\Bolt\Exception\SerializationException;
use GraphAware\Bolt\Protocol\Constants;

class Packer
{
    /**
     * @param $v
     * @return string
     */
    public function pack($v)
    {
        $stream = '';
        if (is_string($v)) {
            $stream .= $this->packText($v);
        } elseif (is_array($v)) {
            $stream .= ($this->isList($v) && !empty($v)) ? $this->packList($v) : $this->packMap($v);
        } elseif (is_int($v)) {
            $stream .= $this->packInteger($v);
        } elseif (is_null($v)) {
            $stream .= chr(Constants::MARKER_NULL);
        } elseif (true === $v) {
            $stream .= chr(Constants::MARKER_TRUE);
        } elseif (false === $v) {
            $stream .= chr(Constants::MARKER_FALSE);
        } elseif (is_float($v)) {
            // packFloat
        }

        return $stream;
    }

    /**
     * @param $length
     * @param $signature
     * @return string
     */
    public function packStructureHeader($length, $signature)
    {
        $stream = '';
        $packedSig = chr($signature);
        if ($length < Constants::SIZE_TINY) {
            $stream .= chr(Constants::STRUCTURE_TINY + $length);
            $stream .= $packedSig;
            return $stream;
        }

        if ($length < Constants::SIZE_MEDIUM) {
            $stream .= chr(Constants::STRUCTURE_MEDIUM);
            $stream .= $this->packUnsignedShortShort($length);
            $stream .= $packedSig;
            return $stream;
        }

        if ($length < Constants::SIZE_LARGE) {
            $stream .= chr(Constants::STRUCTURE_LARGE);
            $stream .= $this->packSignedShort($length);
            $stream .= $packedSig;
            return $stream;
        }

        throw new SerializationException(sprintf('Unable pack the size "%d" of the structure, Out of bound !', $length));
    }

    /**
     * @param $length
     *
     * @return string
     */
    public function getStructureMarker($length)
    {
        $length = (int) $length;
        $bytes = '';
        if ($length < Constants::SIZE_TINY) {
            $bytes .= chr(Constants::STRUCTURE_TINY + $length);
        } elseif ($length < Constants::SIZE_MEDIUM) {
            // do
        } elseif ($length < Constants::SIZE_LARGE) {
            // do
        } else {
            throw new SerializationException(sprintf('Unable to get a Structure Marker for size %d', $length));
        }

        return $bytes;
    }

    /**
     * @param array $array
     * @return string
     */
    public function packList(array $array)
    {
        $size = count($array);
        $b = $this->getListSizeMarker($size);
        foreach ($array as $k => $v) {
            $b .= $this->pack($v);
        }

        return $b;
    }

    /**
     * @param array $array
     * @return bool
     */
    public function isList(array $array)
    {
        foreach ($array as $k => $v) {
            if (!is_int($k)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param $size
     * @return string
     */
    public function getListSizeMarker($size)
    {
        $b = '';
        if ($size < Constants::SIZE_TINY) {
            $b .= chr(Constants::LIST_TINY + $size);
            return $b;
        }
        if ($size < Constants::SIZE_8) {
            $b .= chr(Constants::LIST_8);
            $b .= $this->packUnsignedShortShort($size);
            return $b;
        }
        if ($b < Constants::SIZE_16) {
            $b .= chr(Constants::LIST_16);
            $b .= $this->packUnsignedShort($size);
            return $b;
        }
        if ($b < Constants::SIZE_32) {
            $b .= chr(Constants::LIST_32);
            $b .= $this->packUnsignedLong($size);
            return $b;
        }

        throw new SerializationException(sprintf('Unable to create marker for List size %d', $size));
    }

    /**
     * @param array $array
     * @return string
     */
    public function packMap(array $array)
    {
        $size = count($array);
        $b = '';
        $b .= $this->getMapSizeMarker($size);
        foreach ($array as $k => $v) {
            $b .= $this->pack($k);
            $b .= $this->pack($v);
        }

        return $b;
    }

    /**
     * @param $size
     * @return string
     */
    public function getMapSizeMarker($size)
    {
        $b = '';
        if ($size < Constants::SIZE_TINY) {
            $b .= chr(Constants::MAP_TINY + $size);
            return $b;
        }
        if ($size < Constants::SIZE_8) {
            $b .= chr(Constants::MAP_8);
            $b .= $this->packUnsignedShortShort($size);
            return $b;
        }
        if ($size < Constants::SIZE_16) {
            $b .= chr(Constants::MAP_16);
            $b .= $this->packUnsignedShort($size);
            return $b;
        }
        if ($size < Constants::SIZE_32) {
            $b .= chr(Constants::MAP_32);
            $b .= $this->packUnsignedLong($size);
            return $b;
        }


        throw new SerializationException(sprintf('Unable to pack Array with size %d. Out of bound !', $size));
    }

    /**
     * @param $value
     * @return string
     */
    public function packText($value)
    {
        $value = utf8_decode($value);
        $length = strlen($value);
        $b = '';
        if ($length < 16) {
            $b .= chr(Constants::TEXT_TINY + $length);
            $b .= $value;
            return $b;
        }

        if ($length < 256) {
            $b .= chr(Constants::TEXT_8);
            $b .= $this->packUnsignedShortShort($length);
            $b .= $value;
            return $b;
        }

        if ($length < 65536) {
            $b .= chr(Constants::TEXT_16);
            $b .= $this->packUnsignedShort($length);
            $b .= $value;
            return $b;
        }

        if ($length < PHP_INT_MAX && $length < 4294967295) {
            $b .= chr(Constants::INT_32);
            $b .= $this->packUnsignedLong($length);
            $b .= $value;
            return $b;
        }

        throw new \OutOfBoundsException(sprintf('The value %s can not be packed, length is out of bound', $value));
    }

    /**
     * @return string
     */
    public function getRunSignature()
    {
        return chr(Constants::SIGNATURE_RUN);
    }

    /**
     * @return string
     */
    public function getEndSignature()
    {
        return str_repeat(chr(Constants::MISC_ZERO), 2);
    }

    /**
     * @param $stream
     * @return string
     */
    public function getSizeMarker($stream)
    {
        $size = mb_strlen($stream, 'ASCII');

        return pack('n', $size);
    }

    /**
     * @param $v
     * @return string
     */
    public function packBigEndian($v)
    {
        return pack('N', $v);
    }

    /**
     * @param $value
     * @return string
     */
    public function packInteger($value)
    {
        $value = (int) $value;
        $b = '';
        if ($this->isShortShort($value)) {
            $b .= chr(Constants::INT_8);
            $b .= $this->packSignedShort($value);
            return $b;
        }

        if ($this->isShort($value)) {
            $b .= chr(Constants::INT_8);
            $b .= $this->packSignedShort($value);
            return $b;
        }
    }

    /**
     * @param $integer
     * @return string
     */
    public function packUnsignedShortShort($integer)
    {
        return pack('C', $integer);
    }

    /**
     * @param $integer
     * @return string
     */
    public function packSignedShort($integer)
    {
        return pack('s', $integer);
    }

    /**
     * @param $integer
     * @return string
     */
    public function packUnsignedShort($integer)
    {
        return pack('n', $integer);
    }

    /**
     * @param $integer
     * @return string
     */
    public function packUnsignedLong($integer)
    {
        return pack('N', $integer);
    }

    /**
     * @param $value
     * @return bool
     */
    public function isShortShort($value)
    {
        if (in_array($value, range(-16, 127))) {
            return true;
        }

        return false;
    }

    /**
     * @param $integer
     * @return bool
     */
    public function isShort($integer)
    {
        $min = 128;
        $max = 32767;
        $minMin = -129;
        $minMax = -32768;

        if (in_array($integer, range($min, $max)) || in_array($integer, range($minMin, $minMax))) {
            return true;
        }

        return false;
    }
}