<?php
namespace Fab\Media\FileUpload\Optimizer;

/*
 * This file is part of the Fab/Media project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

/**
 * This is a utility class to set the Exif Orientation Tag.
 * It can be used for automatic orientation correction of digital camera pictures.
 *
 * The Exif orientation value gives the orientation of the camera
 * relative to the scene when the image was captured.  The relation
 * of the '0th row' and '0th column' to visual position is shown as
 * below.
 *
 * Value | 0th Row     | 0th Column
 * ------+-------------+-----------
 *   1   | top         | left side
 *   2   | top         | right side
 *   3   | bottom      | right side
 *   4   | bottom      | left side
 *   5   | left side   | top
 *   6   | right side  | top
 *   7   | right side  | bottom
 *   8   | left side   | bottom
 *
 * For convenience, here is what the letter F would look like if it were
 * tagged correctly and displayed by a program that ignores the orientation
 * tag:
 *
 *   1        2       3      4         5            6           7          8
 *
 * 888888  888888      88  88      8888888888  88                  88  8888888888
 * 88          88      88  88      88  88      88  88          88  88      88  88
 * 8888      8888    8888  8888    88          8888888888  8888888888          88
 * 88          88      88  88
 * 88          88  888888  888888
 *
 * @author 2013 Xavier Perseguers <xavier@causal.ch> based on work http://sylvana.net/jpegcrop/jpegexiforient.c
 */
class JpegExifOrient
{

    /**
     * Sets the Exif Orientation for a given JPG file.
     *
     * @param string $filename
     * @param integer $orientation
     * @return void
     * @throws \RuntimeException
     */
    public static function setOrientation($filename, $orientation)
    {
        $exif_data = [];    // Buffer
        $offsetJfif = 0;

        if (($fh = fopen($filename, 'rb+')) === false) {
            throw new \RuntimeException('Can\'t open ' . $filename, 1363533724);
        }

        // Read file head, check for JPEG SOI + JFIF/Exif APP1
        for ($i = 0; $i < 4; $i++) $exif_data[$i] = self::read_1_byte($fh);
        if ($exif_data[0] !== 0xFF ||
            $exif_data[1] !== 0xD8
        ) {
        }

        // JFIF segment: http://en.wikipedia.org/wiki/JPEG_File_Interchange_Format#JFIF_segment_format
        if ($exif_data[2] === 0xFF && $exif_data[3] === 0xE0) {
            // Get the marker parameter length count
            $length = self::read_2_bytes($fh);
            $offsetJfif = $length + 2;    // "+ 2" to skip the 2 bytes introducing this additional segment
            // Length includes itself, so must be at least 2
            // Following JFIF data length must be at least 6
            if ($length < 8) {
                return;
            }
            $length -= 8;
            // Read JFIF head, check for "JFIF"
            for ($i = 0; $i < 5; $i++) $exif_data[$i] = self::read_1_byte($fh);
            if ($exif_data[0] !== 0x4A ||
                $exif_data[1] !== 0x46 ||
                $exif_data[2] !== 0x49 ||
                $exif_data[3] !== 0x46 ||
                $exif_data[4] !== 0
            ) {
                return;
            }
            // Read JFIF body
            for ($i = 0; $i < $length; $i++) $exif_data[$i] = self::read_1_byte($fh);

            if (self::read_1_byte($fh) !== 0) {
                // Seems there is a 0 byte to separate segments...
                return;
            }
            // Read next 2 bytes in $exif_data[2] and $exif_data[3] as Exif APP1 segment
            // is now expected
            $exif_data[2] = self::read_1_byte($fh);
            $exif_data[3] = self::read_1_byte($fh);
        }

        // Exif APP1
        if ($exif_data[2] !== 0xFF || $exif_data[3] !== 0xE1) {
            return;
        }

        // Get the marker parameter length count
        $length = self::read_2_bytes($fh);
        // Length includes itself, so must be at least 2
        // Following Exif data length must be at least 6
        if ($length < 8) {
            return;
        }
        $length -= 8;
        // Read Exif head, check for "Exif"
        for ($i = 0; $i < 6; $i++) $exif_data[$i] = self::read_1_byte($fh);
        if ($exif_data[0] !== 0x45 ||
            $exif_data[1] !== 0x78 ||
            $exif_data[2] !== 0x69 ||
            $exif_data[3] !== 0x66 ||
            $exif_data[4] !== 0 ||
            $exif_data[5] !== 0
        ) {
            return;
        }
        // Read Exif body
        for ($i = 0; $i < $length; $i++) $exif_data[$i] = self::read_1_byte($fh);

        if ($length < 12) {    // Length of an IFD entry
            return;
        }

        // Discover byte order
        if ($exif_data[0] === 0x49 && $exif_data[1] === 0x49) {
            $is_motorola = false;
        } elseif ($exif_data[0] === 0x4D && $exif_data[1] === 0x4D) {
            $is_motorola = true;
        } else {
            return;
        }

        // Check Tag mark
        if ($is_motorola) {
            if ($exif_data[2] !== 0) return;
            if ($exif_data[3] !== 0x2A) return;
        } else {
            if ($exif_data[3] !== 0) return;
            if ($exif_data[2] !== 0x2A) return;
        }

        // Get first IFD offset (offset to IFD0)
        if ($is_motorola) {
            if ($exif_data[4] !== 0) return;
            if ($exif_data[5] !== 0) return;
            $offset = $exif_data[6];
            $offset <<= 8;
            $offset += $exif_data[7];
        } else {
            if ($exif_data[7] !== 0) return;
            if ($exif_data[6] !== 0) return;
            $offset = $exif_data[5];
            $offset <<= 8;
            $offset += $exif_data[4];
        }
        // Check end of data segment
        if ($offset > $length - 2) return;

        // Get the number of directory entries contained in this IFD
        if ($is_motorola) {
            $number_of_tags = $exif_data[$offset];
            $number_of_tags <<= 8;
            $number_of_tags += $exif_data[$offset + 1];
        } else {
            $number_of_tags = $exif_data[$offset + 1];
            $number_of_tags <<= 8;
            $number_of_tags += $exif_data[$offset];
        }
        if ($number_of_tags === 0) return;
        $offset += 2;

        // Search for Orientation Tag in IFD0
        while (true) {
            // Check end of data segment
            if ($offset > $length - 12) return;
            // Get Tag number
            if ($is_motorola) {
                $tagnum = $exif_data[$offset];
                $tagnum <<= 8;
                $tagnum += $exif_data[$offset + 1];
            } else {
                $tagnum = $exif_data[$offset + 1];
                $tagnum <<= 8;
                $tagnum += $exif_data[$offset];
            }
            // Found Orientation Tag
            if ($tagnum === 0x0112) break;
            if (--$number_of_tags === 0) return;
            $offset += 12;
        }

        // Set the Orientation value
        if ($is_motorola) {
            $exif_data[$offset + 2] = 0;    // Format = unsigned short (2 octets)
            $exif_data[$offset + 3] = 3;
            $exif_data[$offset + 4] = 0;    // Number of Components = 1
            $exif_data[$offset + 5] = 0;
            $exif_data[$offset + 6] = 0;
            $exif_data[$offset + 7] = 1;
            $exif_data[$offset + 8] = 0;
            $exif_data[$offset + 9] = $orientation;
            $exif_data[$offset + 10] = 0;
            $exif_data[$offset + 11] = 0;
        } else {
            $exif_data[$offset + 2] = 3;    // Format = unsigned short (2 octets)
            $exif_data[$offset + 3] = 0;
            $exif_data[$offset + 4] = 1;    // Number of Components = 1
            $exif_data[$offset + 5] = 0;
            $exif_data[$offset + 6] = 0;
            $exif_data[$offset + 7] = 0;
            $exif_data[$offset + 8] = $orientation;
            $exif_data[$offset + 9] = 0;
            $exif_data[$offset + 10] = 0;
            $exif_data[$offset + 11] = 0;
        }
        fseek($fh, (4 + 2 + 6 + 2) + $offsetJfif + $offset, SEEK_SET);
        $data = '';
        for ($i = 0; $i < 10; $i++) {
            $data .= chr($exif_data[$i + $offset + 2]);
        }
        fwrite($fh, $data);
        fclose($fh);
    }

    /**
     * Reads one byte, testing for EOF.
     *
     * @param resource $handle
     * @return integer
     * @throws \RuntimeException
     */
    protected static function read_1_byte($handle)
    {
        $c = fgetc($handle);
        if ($c === false) {
            throw new \RuntimeException('Premature EOF in JPEG file', 1363533326);
        }
        return ord($c);
    }

    /**
     * Reads 2 bytes, converts them to unsigned int
     * Remark: All 2-byte quantities in JPEG markers are MSB first.
     *
     * @param resource $handle
     * @return integer
     * @throws \RuntimeException
     */
    protected static function read_2_bytes($handle)
    {
        $c1 = fgetc($handle);
        if ($c1 === false) {
            throw new \RuntimeException('Premature EOF in JPEG file', 1363533326);
        }
        $c2 = fgetc($handle);
        if ($c2 === false) {
            throw new \RuntimeException('Premature EOF in JPEG file', 1363533326);
        }
        return (ord($c1) << 8) + (ord($c2));
    }

}
