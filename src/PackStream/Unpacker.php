<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) Graph Aware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\PackStream;

use PTS\Bolt\Exception\SerializationException;
use PTS\Bolt\Misc\Helper;
use PTS\Bolt\PackStream\Structure\Structure;
use PTS\Bolt\Protocol\Constants;
use PTS\Bolt\Protocol\Message\RawMessage;
use PTS\Bolt\Result\Type\Node;
use PTS\Bolt\Result\Type\Path;
use PTS\Bolt\Result\Type\Relationship;

class Unpacker
{
    const SUCCESS = 'SUCCESS';

    const FAILURE = 'FAILURE';

    const RECORD = 'RECORD';

    const IGNORED = 'IGNORED';

    /**
     * @var bool
     */
    protected $is64bits;

    /**
     * @var StreamChannel
     */
    protected $streamChannel;

    /**
     * @param StreamChannel $streamChannel
     */
    public function __construct(StreamChannel $streamChannel)
    {
        $this->is64bits = PHP_INT_SIZE == 8;
        $this->streamChannel = $streamChannel;
    }

    /**
     * @param \PTS\Bolt\Protocol\Message\RawMessage $message
     *
     * @return Structure
     */
    public function unpackRaw(RawMessage $message)
    {
        $walker = new BytesWalker($message);

        return $this->unpackElement($walker);
    }

    /**
     * @return Structure
     */
    public function unpack()
    {
        $b = '';
        do {
            $chunkHeader = $this->streamChannel->read(2);
            list(, $size) = unpack('n', $chunkHeader);
            $b .= $this->streamChannel->read($size);
        } while ($size > 0);

        return $this->unpackElement(new BytesWalker(new RawMessage($b)));
    }

    /**
     * @param BytesWalker $walker
     *
     * @return Structure | string | int | array | float | bool
     */
    public function unpackElement(BytesWalker $walker)
    {
        $marker = $walker->read(1);
        $byte = hexdec(bin2hex($marker));
        $ordMarker = ord($marker);
        $markerHigh = $ordMarker & 0xf0;
        $markerLow = $ordMarker & 0x0f;

        // Structures
        if (0xb0 <= $ordMarker && $ordMarker <= 0xbf) {
            $walker->rewind(1);
            $structureSize = $this->getStructureSize($walker);
            $sig = $this->getSignature($walker);
            $str = new Structure($sig, $structureSize);
            $done = 0;
            while ($done < $structureSize) {
                $elt = $this->unpackElement($walker);
                $str->addElement($elt);
                ++$done;
            }

            return $str;
        }

        if ($markerHigh === Constants::MAP_TINY) {
            $size = $markerLow;
            $map = [];
            for ($i = 0; $i < $size; ++$i) {
                $identifier = $this->unpackElement($walker);
                $value = $this->unpackElement($walker);
                $map[$identifier] = $value;
            }

            return $map;
        }

        if (Constants::MAP_8 === $byte) {
            $size = $this->readUnsignedShortShort($walker);

            return $this->unpackMap($size, $walker);
        }

        if ($byte === Constants::MAP_16) {
            $size = $this->readUnsignedShort($walker);

            return $this->unpackMap($size, $walker);
        }

        if ($byte === Constants::MAP_32) {
            $size = $this->readUnsignedLong($walker);

            return $this->unpackMap($size, $walker);
        }

        if ($markerHigh === Constants::TEXT_TINY) {
            $textSize = $this->getLowNibbleValue($marker);

            return $this->unpackText($textSize, $walker);
        }

        if ($byte === Constants::TEXT_8) {
            $textSize = $this->readUnsignedShortShort($walker);

            return $this->unpackText($textSize, $walker);
        }

        if ($byte === Constants::TEXT_16) {
            $textSize = $this->readUnsignedShort($walker);

            return $this->unpackText($textSize, $walker);
        }

        if ($byte === Constants::TEXT_32) {
            $textSize = $this->readUnsignedLong($walker);

            return $this->unpackText($textSize, $walker);
        }

        if ($byte === Constants::INT_8) {
            $integer = $this->readSignedShortShort($walker);

            return $this->unpackInteger($integer);
        }

        if ($byte === Constants::INT_16) {
            $integer = $this->readSignedShort($walker);

            return $this->unpackInteger($integer);
        }

        if ($byte === Constants::INT_32) {
            $integer = $this->readSignedLong($walker);

            return $this->unpackInteger($integer);
        }

        if ($byte === Constants::INT_64) {
            $integer = $this->readSignedLongLong($walker);

            return $this->unpackInteger($integer);
        }

        if ($markerHigh === Constants::LIST_TINY) {
            $size = $this->getLowNibbleValue($marker);

            return $this->unpackList($size, $walker);
        }

        if ($byte === Constants::LIST_8) {
            $size = $this->readUnsignedShortShort($walker);

            return $this->unpackList($size, $walker);
        }

        if ($byte === Constants::LIST_16) {
            $size = $this->readUnsignedShort($walker);

            return $this->unpackList($size, $walker);
        }

        if ($byte === Constants::LIST_32) {
            $size = $this->readUnsignedLong($walker);

            return $this->unpackList($size, $walker);
        }

        // Checks for TINY INTS
        if ($this->isInRange(0x00, 0x7f, $marker) || $this->isInRange(0xf0, 0xff, $marker)) {
            $walker->rewind(1);
            $integer = $this->readSignedShortShort($walker);

            return $this->unpackInteger($integer);
        }

        // Checks for floats
        if ($byte === Constants::MARKER_FLOAT) {
            list(, $v) = unpack('d', strrev($walker->read(8)));

            return (float)$v;
        }

        // Checks Primitive Values NULL, TRUE, FALSE
        if ($byte === Constants::MARKER_NULL) {
            return null;
        }

        if ($byte === Constants::MARKER_TRUE) {
            return true;
        }

        if ($byte === Constants::MARKER_FALSE) {
            return false;
        }

        throw new SerializationException(
            sprintf('Unable to find serialization type for marker %s', Helper::prettyHex($marker))
        );
    }

    /**
     * @param BytesWalker $walker
     *
     * @return Node
     */
    public function unpackNode(BytesWalker $walker)
    {
        $identity = $this->unpackElement($walker);
        $labels = $this->unpackElement($walker);
        $properties = $this->unpackElement($walker);

        return new Node($identity, $labels, $properties);
    }

    /**
     * @param BytesWalker $walker
     *
     * @return Relationship
     */
    public function unpackRelationship(BytesWalker $walker)
    {
        $identity = $this->unpackElement($walker);
        $startNode = $this->unpackElement($walker);
        $endNode = $this->unpackElement($walker);
        $type = $this->unpackElement($walker);
        $properties = $this->unpackElement($walker);

        return new Relationship($identity, $startNode, $endNode, $type, $properties);
    }

    /**
     * @param BytesWalker $walker
     *
     * @return Path
     */
    public function unpackPath(BytesWalker $walker)
    {
        return $this->unpackElement($walker);
    }

    /**
     * @param int $size
     * @param BytesWalker $walker
     *
     * @return string
     */
    public function unpackText($size, BytesWalker $walker)
    {
        return $walker->read($size);
    }

    /**
     * @param string $value
     *
     * @return int
     */
    public function unpackInteger($value)
    {
        return (int)$value;
    }

    /**
     * @param int $size
     * @param BytesWalker $walker
     *
     * @return array
     */
    public function unpackMap($size, BytesWalker $walker)
    {
        $map = [];
        for ($i = 0; $i < $size; ++$i) {
            $identifier = $this->unpackElement($walker);
            $value = $this->unpackElement($walker);
            $map[$identifier] = $value;
        }

        return $map;
    }

    /**
     * @param int $size
     * @param BytesWalker $walker
     *
     * @return array
     */
    public function unpackList($size, BytesWalker $walker)
    {
        $size = (int)$size;
        $list = [];
        for ($i = 0; $i < $size; ++$i) {
            $list[] = $this->unpackElement($walker);
        }

        return $list;
    }

    /**
     * @param BytesWalker $walker
     *
     * @return int
     */
    public function getStructureSize(BytesWalker $walker)
    {
        $marker = $walker->read(1);

        // if tiny size, no more bytes to read, the size is encoded in the low nibble
        if ($this->isMarkerHigh($marker, Constants::STRUCTURE_TINY)) {
            return $this->getLowNibbleValue($marker);
        }
        $ordMarker = ord($marker);
        if ($ordMarker == Constants::STRUCTURE_MEDIUM) {
            return $this->readUnsignedShortShort($walker);
        }
        return $this->readUnsignedShort($walker);
    }

    /**
     * @param BytesWalker $walker
     *
     * @return string
     */
    public function getSignature(BytesWalker $walker)
    {
        static $signatures = [
            Constants::SIGNATURE_SUCCESS => self::SUCCESS,
            Constants::SIGNATURE_FAILURE => self::FAILURE,
            Constants::SIGNATURE_RECORD => self::RECORD,
            Constants::SIGNATURE_IGNORE => self::IGNORED,
            Constants::SIGNATURE_UNBOUND_RELATIONSHIP => Structure::SIGNATURE_UNBOUND_RELATIONSHIP,
            Constants::SIGNATURE_NODE => Structure::SIGNATURE_NODE,
            Constants::SIGNATURE_PATH => Structure::SIGNATURE_PATH,
            Constants::SIGNATURE_RELATIONSHIP => Structure::SIGNATURE_RELATIONSHIP,
            Constants::SIGNATURE_DATE => Structure::SIGNATURE_DATE,
            Constants::SIGNATURE_DATE_TIME_OFFSET => Structure::SIGNATURE_DATE_TIME_OFFSET,
            Constants::SIGNATURE_DATE_TIME_ZONED => Structure::SIGNATURE_DATE_TIME_ZONED,
            Constants::SIGNATURE_DURATION => Structure::SIGNATURE_DURATION,
            Constants::SIGNATURE_TIME => Structure::SIGNATURE_TIME,
            Constants::SIGNATURE_LOCAL_TIME => Structure::SIGNATURE_LOCAL_TIME,
            Constants::SIGNATURE_LOCAL_DATE_TIME => Structure::SIGNATURE_LOCAL_DATE_TIME,
            Constants::SIGNATURE_POINT2D => Structure::SIGNATURE_POINT2D,
            Constants::SIGNATURE_POINT3D => Structure::SIGNATURE_POINT3D
        ];

        $sigMarker = $walker->read(1);
        $ordMarker = ord($sigMarker);

        return $signatures[$ordMarker];
    }

    /**
     * @param string $byte
     *
     * @return int
     */
    public function getLowNibbleValue($byte)
    {
        $marker = ord($byte);

        return $marker & 0x0f;
    }

    /**
     * @param $byte
     * @param $nibble
     *
     * @return bool
     */
    public function isMarker($byte, $nibble)
    {
        $marker_raw = hexdec(bin2hex($byte));

        return $marker_raw === $nibble;
    }

    /**
     * @param $byte
     * @param $nibble
     *
     * @return bool
     */
    public function isMarkerHigh($byte, $nibble)
    {
        $marker_raw = ord($byte);
        $marker = $marker_raw & 0xF0;

        return $marker === $nibble;
    }

    /**
     * @param $sig
     * @param $byte
     *
     * @return bool
     */
    public function isSignature($sig, $byte)
    {
        return $sig === ord($byte);
    }

    /**
     * @param BytesWalker $walker
     *
     * @return mixed
     */
    public function readUnsignedShortShort(BytesWalker $walker)
    {
        list(, $v) = unpack('C', $walker->read(1));

        return $v;
    }

    /**
     * @param BytesWalker $walker
     *
     * @return mixed
     */
    public function readSignedShortShort(BytesWalker $walker)
    {
        list(, $v) = unpack('c', $walker->read(1));

        return $v;
    }

    /**
     * @param BytesWalker $walker
     *
     * @return mixed
     */
    public function readUnsignedShort(BytesWalker $walker)
    {
        list(, $v) = unpack('n', $walker->read(2));

        return $v;
    }

    /**
     * @param BytesWalker $walker
     *
     * @return mixed
     */
    public function readSignedShort(BytesWalker $walker)
    {
        list(, $v) = unpack('s', $this->correctEndianness($walker->read(2)));

        return $v;
    }

    /**
     * @param BytesWalker $walker
     *
     * @return mixed
     */
    public function readUnsignedLong(BytesWalker $walker)
    {
        list(, $v) = unpack('N', $walker->read(4));

        return sprintf('%u', $v);
    }

    /**
     * @param BytesWalker $walker
     *
     * @return mixed
     */
    public function readSignedLong(BytesWalker $walker)
    {
        list(, $v) = unpack('l', $this->correctEndianness($walker->read(4)));

        return $v;
    }

    /**
     * @param BytesWalker $walker
     *
     * @return mixed
     */
    public function readUnsignedLongLong(BytesWalker $walker)
    {
        list(, $v) = unpack('J', $walker->read(8));

        return $v;
    }

    /**
     * @param BytesWalker $walker
     *
     * @return int
     */
    public function readSignedLongLong(BytesWalker $walker)
    {
        list(, $high, $low) = unpack('N2', $walker->read(8));

        return (int)bcadd($high << 32, $low, 0);
    }

    /**
     * @param int $start
     * @param int $end
     * @param string $byte
     *
     * @return mixed
     */
    public function isInRange($start, $end, $byte)
    {
        $range = range($start, $end);

        return in_array(ord($byte), $range);
    }

    /**
     * @param string $byteString
     *
     * @return string
     */
    private function correctEndianness($byteString)
    {
        $tmp = unpack('S', "\x01\x00");
        $isLittleEndian = $tmp[1] == 1;

        return $isLittleEndian ? strrev($byteString) : $byteString;
    }

    /**
     * @param int $longInt
     *
     * @return bool
     */
    private static function getLongMSB($longInt)
    {
        return (bool)($longInt & 0x80000000);
    }
}
