<?php

/**
 *  A TTS API for OpenKarotz using IBM Watson API.
 *
 * @author:  Ludovic Toinel <ludovic@toinel.com>
 */

require 'lib/watson.php';
require 'lib/encoder.php';

// Get Text parameters
if (isset($_GET['text'])) {
    $text = $_GET['text'];
} else {
    throw new Exception("Missing 'text' argument");
}

// Get Format parameters
if (isset($_GET['format'])) {
    $format = $_GET['format'];
} else {
    $format = "ogg";
}

// Checking the format
if ($format == "ogg") {
    $extension = "ogg";

} else if ($format == "wave") {
    $extension = "wav";

} else if ($format == "mp3") {
    $extension = "mp3";

} else {
    throw new Exception("Unknown 'text' argument");

}

// Building the sound file path
$sound_file_path = "./tmp/$id.$extension";

// Check if the file already exists in the temp directory
if (file_exists($sound_file_path)) {
    $response = file_get_contents($sound_file_path);
    header('Content-Disposition: attachment; filename="' . $id . '.' . $extension . '"');
    echo $response;
    exit;
}

// Voice synthesis
$download_file_path = voiceSynthesis($text, $format);

// If MP3 format is requested
if ($format == "mp3") {
    encodeToMp3($download_file_path, $sound_file_path);

}

$response = file_get_contents($sound_file_path);
header('Content-Disposition: attachment; filename="' . $id . '.' . $extension . '"');

echo $response;
exit;

