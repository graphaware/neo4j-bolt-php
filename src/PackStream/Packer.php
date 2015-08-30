<?php
/*
 * This file is part of the Neo4j PackStream package.
 *
 * (c) Christophe Willemsen <willemsen.christophe@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Bolt\PackStream;

use GraphAware\Bolt\Exception\SerializationException;
use GraphAware\Bolt\Misc\Helper;
use GraphAware\Bolt\Protocol\Constants;

class Packer
{
    const SIGNATURE_RUN = 0x10;
    const SIGNATURE_PULL_ALL = 0x3f;
    const SIGNATURE_INIT = 0x01;
    const SIGNATURE_SUCCESS = 0x70;
    const SIGNATURE_RECORD = 0x71;
    const SIGNATURE_IGNORE = 0x7e;
    const SIGNATURE_FAILURE = 0x7f;

    const STRUCTURE_TINY = 0xb0;
    const STRUCTURE_MEDIUM = 0xdc;
    const STRUCTURE_LARGE = 0xdd;

    const TEXT_TINY = 0x80;
    const TEXT_REGULAR = 0xd0;

    const MAP_TINY = 0xa0;

    const SIZE_TINY = 16;
    const SIZE_MEDIUM = 256;
    const SIZE_LARGE = 65536;

    const MISC_ZERO = 0x00;

    const MARKER_NULL = 0xc0;

    const MARKER_TRUE = 0xc3;

    const MARKER_FALSE = 0xc2;

    public function pack($v)
    {
        $stream = '';
        if (is_string($v)) {
            $stream .= $this->packText($v);
        } elseif (is_array($v)) {
            $size = count($v);
            $stream .= $this->getMapMarker($size);
            foreach ($v as $k => $value) {
                $stream .= $this->pack($k);
                $stream .= $this->pack($value);
            }
        } elseif (is_int($v)) {
            $stream .= $this->packInteger($v);
        }

        return $stream;
    }

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

    public function getTextMarker($length)
    {
        if ($length < self::SIZE_TINY) {
            return hex2bin(dechex(self::TEXT_TINY + $length));
        } else {
            $marker = chr(self::TEXT_REGULAR);
            $marker .= pack('c', $length);

            return $marker;
        }
    }

    public function getMapMarker($length)
    {
        if ($length < self::SIZE_TINY) {
            return hex2bin(dechex(self::MAP_TINY + $length));
        }
    }

    public function getRunSignature()
    {
        return chr(self::SIGNATURE_RUN);
    }

    public function getEndSignature()
    {
        return chr(self::MISC_ZERO) . chr(self::MISC_ZERO);
    }

    public function getSizeMarker($stream)
    {
        $size = mb_strlen($stream, 'ASCII');

        return pack('n', $size);
    }

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

    public function packBigEndian($v)
    {
        return pack('N', $v);
    }

    public function packInteger($value)
    {
        $value = (int) $value;
        if ($this->isShortShort($value)) {
            // tiny ints
        }

        if ($this->isShort($value)) {
            $b = '';
            $b .= chr(Constants::INT_8);
            $b .= $this->packSignedShort($value);
            return $b;
        }

    }

    public function packUnsignedShortShort($integer)
    {
        return pack('C', $integer);
    }

    public function packSignedShort($integer)
    {
        return pack('s', $integer);
    }

    public function packUnsignedShort($integer)
    {
        return pack('n', $integer);
    }

    public function packUnsignedLong($integer)
    {
        return pack('N', $integer);
    }

    public function isShortShort($value)
    {
        if (in_array($value, range(-16, 127))) {
            return true;
        }

        return false;
    }

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