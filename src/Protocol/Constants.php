<?php

/*
 * This file is part of the GraphAware Bolt package.
 *
 * (c) GraphAware Ltd <christophe@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PTS\Bolt\Protocol;

class Constants
{
    // SIGNATURES

    const SIGNATURE_RUN = 0x10;

    const SIGNATURE_PULL_ALL = 0x3f;

    const SIGNATURE_DISCARD_ALL = 0x2f;

    const SIGNATURE_PULL = 0x3f; // V4

    const SIGNATURE_DISCARD = 0x2f; // V4

    const SIGNATURE_SUCCESS = 0x70;

    const SIGNATURE_INIT = 0x01;

    const SIGNATURE_HELLO = 0x01; // V3

    const SIGNATURE_GOODBYE = 0x01; // V3

    const SIGNATURE_BEGIN = 0x11; // V3

    const SIGNATURE_COMMIT = 0x12; // V3

    const SIGNATURE_ROLLBACK = 0x13; // V3

    const SIGNATURE_FAILURE = 0x7f;

    const SIGNATURE_ACK_FAILURE = 0x0f;

    const SIGNATURE_IGNORE = 0x7E;

    const SIGNATURE_RECORD = 0x71;

    const SIGNATURE_NODE = 0x4e;

    const SIGNATURE_RELATIONSHIP = 0x52;

    const SIGNATURE_PATH = 0x50;

    const SIGNATURE_UNBOUND_RELATIONSHIP = 0x72;

    // STRUCTURES

    const STRUCTURE_TINY = 0xb0;

    const STRUCTURE_MEDIUM = 0xdc;

    const STRUCTURE_LARGE = 0xdd;

    // TEXTS

    const TEXT_TINY = 0x80;

    const TEXT_8 = 0xd0;

    const TEXT_16 = 0xd1;

    const TEXT_32 = 0xd2;

    // INTEGERS

    const INT_8 = 0xc8;

    const INT_16 = 0xc9;

    const INT_32 = 0xca;

    const INT_64 = 0xcb;

    // MAPS

    const MAP_TINY = 0xa0;

    const MAP_8 = 0xd8;

    const MAP_16 = 0xd9;

    const MAP_32 = 0xda;

    // LISTS

    const LIST_TINY = 0x90;

    const LIST_8 = 0xd4;

    const LIST_16 = 0xd5;

    const LIST_32 = 0xd6;

    // SIZES

    const SIZE_TINY = 16;

    const SIZE_8 = 256;

    const SIZE_16 = 65536;

    const SIZE_32 = 4294967295;

    const SIZE_MEDIUM = 256;

    const SIZE_LARGE = 65536;

    // FLOAT

    const MARKER_FLOAT = 0xc1;

    // POINTS (V2+)

    const MARKER_POINT2D = 0xb3;

    const SIGNATURE_POINT2D = 0x58;

    const MARKER_POINT3D = 0xb4;

    const SIGNATURE_POINT3D = 0x59;

    // TEMPORAL (V2+)

    const MARKER_DATE = 0xB1;

    const SIGNATURE_DATE = 0x44;

    const MARKER_DATE_TIME_OFFSET = 0xB3;

    const SIGNATURE_DATE_TIME_OFFSET = 0x46;

    const MARKER_DATE_TIME_ZONED = 0xB3;

    const SIGNATURE_DATE_TIME_ZONED = 0x66;

    const MARKER_DURATION = 0xB4;

    const SIGNATURE_DURATION = 0x45;

    const MARKER_TIME = 0xB2;

    const SIGNATURE_TIME = 0x54;

    const MARKER_LOCAL_TIME = 0xB1;

    const SIGNATURE_LOCAL_TIME = 0x74;

    const MARKER_LOCAL_DATE_TIME = 0xB2;

    const SIGNATURE_LOCAL_DATE_TIME = 0x64;

    // MISC

    const MISC_ZERO = 0x00;

    const MARKER_NULL = 0xc0;

    const MARKER_TRUE = 0xc3;

    const MARKER_FALSE = 0xc2;
}
