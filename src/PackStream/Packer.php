<?php
/*
 * This file is part of the Neo4j PackStream package.
 *
 * (c) Christophe Willemsen <willemsen.christophe@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Neo4j\PackStream\PackStream;

use GraphAware\Bolt\Exception\SerializationException;
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
            $v = utf8_encode($v);
            $l = mb_strlen($v, 'ASCII');
            $stream .= $this->getTextMarker($l);
            $stream .= $v;
        } elseif (is_array($v)) {
            $size = count($v);
            $stream .= $this->getMapMarker($size);
        }

        return $stream;
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

    public function getPullAllMessage()
    {
        $msg = $this->getStructureMarker(0);
        $msg .= chr(self::SIGNATURE_PULL_ALL);
        $length = $this->getSizeMarker($msg);

        return $length . $msg . $this->getEndSignature();
    }

    public function getSizeMarker($stream)
    {
        $size = mb_strlen($stream, 'ASCII');

        return pack('n', $size);
    }

    public function packBigEndian($v)
    {
        return pack('N', $v);
    }

    public function getStandardQueryStructureMarker()
    {
        return hex2bin(dechex(self::STRUCTURE_TINY + 2));
    }

    public function getMessages($signature, array $fields = array())
    {
        $stream = '';
        $stream .= $this->packStructureHeader(count($fields), $signature);
        foreach ($fields as $field) {
            $stream .= $this->pack($field);
        }

        return $stream;

    }

    public function packStructureHeader($length, $signature)
    {
        $stream = '';
        $packedSig = chr($signature);
        if ($length < self::SIZE_TINY) {
            $stream .= chr(self::STRUCTURE_TINY + $length);
            $stream .= $packedSig;
        }

        return $stream;
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
}