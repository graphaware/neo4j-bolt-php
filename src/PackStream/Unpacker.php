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
use GraphAware\Bolt\Misc\Helper;
use GraphAware\Bolt\PackStream\Structure\IntegerElement;
use GraphAware\Bolt\PackStream\Structure\ListCollection;
use GraphAware\Bolt\PackStream\Structure\Map;
use GraphAware\Bolt\PackStream\Structure\MessageStructure;
use GraphAware\Bolt\PackStream\Structure\Node;
use GraphAware\Bolt\PackStream\Structure\Relationship;
use GraphAware\Bolt\PackStream\Structure\SimpleElement;
use GraphAware\Bolt\PackStream\Structure\TextElement;
use GraphAware\Bolt\Protocol\Constants;
use GraphAware\Bolt\Protocol\Message\RawMessage;

class Unpacker
{
    const SUCCESS = 'SUCCESS';

    const FAILURE = 'FAILURE';

    const RECORD = 'RECORD';

    const IGNORED = 'IGNORED';

    public function unpackRaw(RawMessage $message)
    {
        $walker = new BytesWalker($message);
        $structSize = $this->getStructureSize($walker);
        $signature = $this->getSignature($walker);
        $structure = new MessageStructure($signature, $structSize);
        for($i = 0; $i < $structSize; ++$i) {
            $structure->addElement($this->unpackElement($walker));
        }

        return $structure;
    }

    /**
     * @param \GraphAware\Bolt\PackStream\BytesWalker $walker
     * @return \GraphAware\Bolt\PackStream\Structure\AbstractElement
     */
    public function unpackElement(BytesWalker $walker)
    {
        $marker = $walker->read(1);

        // Structures
        if ($this->isInRange(0xb0, 0xbf, $marker)) {
            $walker->rewind(1);
            $structureSize = $this->getStructureSize($walker);
            $signatureByte = ord($walker->read(1));
            switch ($signatureByte) {
                case Constants::SIGNATURE_NODE:
                    return $this->unpackNode($walker);
                case Constants::SIGNATURE_RELATIONSHIP:
                    return $this->unpackRelationship($walker);
                default:
                    throw new SerializationException(sprintf('Unable to unpack structure from byte %s', Helper::prettyHex($marker)));
            }
        }

        if ($this->isMarkerHigh($marker, Constants::MAP_TINY)) {
            $size = $this->getLowNibbleValue($marker);
            return $this->unpackMap($size, $walker);
        }

        if ($this->isMarkerHigh($marker, Constants::TEXT_TINY)) {
            $textSize = $this->getLowNibbleValue($marker);

            return $this->unpackText($textSize, $walker);
        }

        if ($this->isMarker($marker, Constants::TEXT_8)) {
            $textSize = $this->readUnsignedShortShort($walker);

            return $this->unpackText($textSize, $walker);
        }

        if ($this->isMarker($marker, Constants::TEXT_16)) {
            $textSize = $this->readUnsignedShort($walker);

            return $this->unpackText($textSize, $walker);
        }

        if ($this->isMarker($marker, Constants::TEXT_32)) {
            $textSize = $this->readUnsignedLong($walker);

            return $this->unpackText($textSize, $walker);
        }

        if ($this->isMarker($marker, Constants::INT_8)) {
            $integer = $this->readSignedShortShort($walker);

            return $this->unpackInteger($integer);
        }

        if ($this->isMarker($marker, Constants::INT_16)) {
            $integer = $this->readSignedShort($walker);

            return $this->unpackInteger($integer);
        }

        if ($this->isMarker($marker, Constants::INT_32)) {
            $integer = $this->readUnsignedLong($walker);

            return $this->unpackInteger($integer);
        }

        if ($this->isMarker($marker, Constants::INT_64)) {
            $integer = $this->readUnsignedLongLong($walker);

            return $this->unpackInteger($integer);
        }

        if ($this->isMarkerHigh($marker, Constants::LIST_TINY)) {
            $size = $this->getLowNibbleValue($marker);
            return $this->unpackList($size, $walker);
        }

        // Checks for TINY INTS
        if ($this->isInRange(0x00, 0x7f, $marker) || $this->isInRange(0xf0, 0xff, $marker)) {
            $walker->rewind(1);
            $integer = $this->readSignedShortShort($walker);

            return $this->unpackInteger($integer);
        }

        // Checks Primitive Values NULL, TRUE, FALSE
        if ($this->isMarker($marker, Constants::MARKER_NULL)) {
            return new SimpleElement(null);
        }

        if ($this->isMarker($marker, Constants::MARKER_TRUE)) {
            return new SimpleElement(true);
        }

        if ($this->isMarker($marker, Constants::MARKER_FALSE)) {
            return new SimpleElement(false);
        }

        throw new SerializationException(sprintf('Unable to find serialization type for marker %s', Helper::prettyHex($marker)));
    }

    public function unpackNode(BytesWalker $walker)
    {
        $identity = $this->unpackElement($walker);
        $labels = $this->unpackElement($walker);
        $properties = $this->unpackElement($walker);

        return new Node($identity, $labels, $properties);
    }

    public function unpackRelationship(BytesWalker $walker)
    {
        $identity = $this->unpackElement($walker);
        $startNode = $this->unpackElement($walker);
        $endNode = $this->unpackElement($walker);
        $type = $this->unpackElement($walker);
        $properties = $this->unpackElement($walker);

        return new Relationship($identity, $startNode, $endNode, $type, $properties);
    }

    public function unpackText($size, BytesWalker $walker)
    {
        $textString = $walker->read($size);

        return new SimpleElement($textString);
    }

    public function unpackInteger($value)
    {
        return new SimpleElement($value);
    }

    public function unpackMap($size, BytesWalker $walker)
    {
        $map = new Map($size);
        for ($i = 0; $i < $size; ++$i) {
            $identifier = $this->unpackElement($walker);
            $value = $this->unpackElement($walker);
            $map->set($identifier->getValue(), $value);
        }

        return $map;
    }

    public function unpackList($size, BytesWalker $walker)
    {
        $size = (int) $size;
        $list = new ListCollection();
        for ($i = 0; $i < $size; ++$i) {
            $element = $this->unpackElement($walker);
            if ($element) {
                $list->add($element);
            }
        }

        return $list;
    }

    public function unpack($v, BytesWalker $walker = null)
    {

    }

    public function getStructureSize(BytesWalker $walker)
    {
        $marker = $walker->read(1);
        // if tiny size, no more bytes to read, the size is encoded in the low nibble
        if ($this->isMarkerHigh($marker, Constants::STRUCTURE_TINY)) {
            return $this->getLowNibbleValue($marker);
        }
    }

    public function getSignature(BytesWalker $walker)
    {
        $sigMarker = $walker->read(1);
        if ($this->isSignature(Constants::SIGNATURE_SUCCESS, $sigMarker)) {
            return self::SUCCESS;
        }

        if ($this->isSignature(Constants::SIGNATURE_FAILURE, $sigMarker)) {
            return self::FAILURE;
        }

        if ($this->isSignature(Constants::SIGNATURE_RECORD, $sigMarker)) {
            return self::RECORD;
        }

        if ($this->isSignature(Constants::SIGNATURE_IGNORE, $sigMarker)) {
            return self::IGNORED;
        }

        throw new SerializationException(sprintf('Unable to guess the signature for byte "%s"', Helper::prettyHex($sigMarker)));
    }

    public function getLowNibbleValue($byte)
    {
        $marker = ord($byte);

        return $marker & 0x0f;
    }

    public function isMarker($byte, $nibble)
    {

        $marker_raw = hexdec(bin2hex($byte));

        return $marker_raw === $nibble;
    }

    public function isMarkerHigh($byte, $nibble)
    {

        $marker_raw = ord($byte);
        $marker = $marker_raw & 0xF0;

        return $marker === $nibble;
    }

    public function isSignature($sig, $byte)
    {
        $raw = ord($byte);

        return $sig === $raw;
    }

    public function readUnsignedShortShort(BytesWalker $walker)
    {
        list(, $v) = unpack('C', $walker->read(1));

        return $v;
    }

    public function readSignedShortShort(BytesWalker $walker)
    {
        list(, $v) = unpack('c', $walker->read(1));

        return $v;
    }

    public function readUnsignedShort(BytesWalker $walker)
    {
        list(, $v) = unpack('n', $walker->read(2));

        return $v;
    }

    public function readSignedShort(BytesWalker $walker)
    {
        list(, $v) = unpack('s', $this->correctEndianness($walker->read(2)));

        return $v;
    }

    public function readUnsignedLong(BytesWalker $walker)
    {
        list(, $v) = unpack('N', $walker->read(4));

        return sprintf('%u', $v);
    }

    public function readSignedLong(BytesWalker $walker)
    {
        list(, $v) = unpack('l', $this->correctEndianness($walker->read(4)));

        return $v;
    }

    public function readUnsignedLongLong(BytesWalker $walker)
    {
        list(, $v) = unpack('J', $walker->read(8));

        return $v;
    }

    public function readSignedLongLong(BytesWalker $walker)
    {
        list(, $v) = unpack('q', $walker->read(8));

        return $v;
    }

    public function isInRange($start, $end, $byte)
    {
        $range = range($start, $end);

        return in_array(ord($byte), $range);
    }

    private function correctEndianness($byteString)
    {
        $tmp = unpack('S', "\x01\x00");
        $isLittleEndian = $tmp[1] == 1;

        return $isLittleEndian ? strrev($byteString) : $byteString;
    }
}