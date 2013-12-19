<?php

/**
 * PHP Interleaved 2 of 5 Barcode
 */
class I2of5
{
    // barcode sequence settings
    private static $start = '0000';
    private static $end = '100';

    // barcode sequences from 0 to 9
    private static $seqs = array(
        '00110',
        '10001',
        '01001',
        '11000',
        '00101',
        '10100',
        '01100',
        '00011',
        '10010',
        '01010'
    );

    // picture height in pixels
    public static $height = 50;

    // bars weight in pixels
    public static $thin = 1;
    public static $wide = 3;

    /**
     * Returns the interleaved 2 of 5 sequence
     * 0 = thin line
     * 1 = wide line
     *
     * @param string $num
     */
    private static function sequence($num)
    {
        $num = trim($num);

        // we need an even length number
        if (strlen($num) %2 != 0) {
            $num = '0' . $num;
        }

        // create the sequence
        $seq = '';
        for ($i = 0, $n = strlen($num); $i < $n; $i++) {
            $seq .= self::$seqs[$num[$i]];
        }

        // interleave the sequence
        $int = '';
        for ($i = 0, $n = strlen($seq); $i < $n - 5; $i++) {
            $int .= $seq[$i] . $seq[$i + 5];
            if (($i + 1) % 5 == 0) {
                $i += 5;
            }
        }

        return self::$start . $int . self::$end;
    }

    /**
     * Creates the barcode png picture
     *
     * @param string $num
     */
    public static function picture($num)
    {
        $seq = self::sequence($num);

        // determine the image width
        $width = 0;
        for ($i = 0, $n = strlen($seq); $i < $n; $i++) {
            $width += $seq[$i] == '0' ? self::$thin : self::$wide;
        }

        $img = imagecreate($width, self::$height);

        $black = imagecolorallocate($img, 0, 0, 0);
        $white = imagecolorallocate($img, 255, 255, 255);

        $x = 0;
        for ($i = 0, $n = strlen($seq); $i < $n; $i++) {

            // line weight
            $w = $seq[$i] == '0' ? self::$thin : self::$wide;

            // line x position
            $x += $w;

            // line color
            $color = $i % 2 == 0 ? $black : $white;

            // draw the line
            imagefilledrectangle($img, $x - $w, 0, $x, self::$height, $color);
        }

        // send the png image
        header("Content-Type: image/png");
        imagepng($img);
    }
}
