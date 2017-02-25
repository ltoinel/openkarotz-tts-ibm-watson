<?php

use Lame\Lame;
use Lame\Settings;

/**
 * Encode a wave to MP3 file.
 *
 * @param string $input_file
 *    The Input file to convert.
 * @param string $output_file
 *    The output file to produce.
 * @return boolean
 *    true if the file has been processed.
 */
function encodeToMp3($input_file, $output_file)
{
    // The path to Lame
    $lame_path = "/usr/bin/lame";

    // Encoding type.
    $encoding = new Settings\Encoding\Preset();
    $encoding->setType(Settings\Encoding\Preset::TYPE_STANDARD);

    // Lame settings.
    $settings = new Settings\Settings($encoding, array(
        '-q'        => 0,
        '-m'        => 'm',
        '--scale'   => 1.5,
    ));

    // Create a new Lame instance.
    $lame = new Lame($lame_path, $settings);

    // Encode using lame.
    $lame->encode($input_file, $output_file, function ($in, $out) {
        unlink($in);
    });

    return true;
}
