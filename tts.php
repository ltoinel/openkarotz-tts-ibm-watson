<?php

/**
 *  TTS API for OpenKarotz using IBM Watson API.
 */

require 'vendor/autoload.php';

use Lame\Lame;
use Lame\Settings;


// Get parameters
if (isset($_GET['text'])){
  $text = $_GET['text'];
} else {
  throw new Exception("Missing 'text' argument");
}

// Get parameters
if (isset($_GET['format'])){
  $format = $_GET['format'];
} else {
  $format = "ogg"; 
}

// IBM Watson API
if($format == "ogg"){
  $header_accept = "Accept: audio/ogg;codecs=opus";
  $param_accept = NULL; 
  $extension = "ogg";
  $download_extension = "ogg";

} else if ($format == "wave"){
  $header_accept = "Accept: audio/wav";
  $param_accept = "&accept=audio%2Fwav";
  $extension = "wav";
  $download_extension = "wav";

} else if ($format == "mp3"){
  $header_accept = "Accept: audio/wav";
  $param_accept = "&accept=audio%2Fwav";
  $extension = "mp3";
  $download_extension = "wav";
}

// Generate an ID
$id = md5($text);
$sound_file_path = "./tmp/$id.$extension";
$download_file_path = "./tmp/$id.$download_extension";

// Check if the file already exists
if (file_exists($sound_file_path)){
  $response = file_get_contents($sound_file_path);
  header('Content-Disposition: attachment; filename="'.$id.'.'.$extension.'"');
  echo $response;
  exit;
}

$watson_url = "https://watson-api-explorer.mybluemix.net/text-to-speech/api/v1/synthesize?voice=fr-FR_ReneeVoice&text=".urlencode($text).$param_accept;

$s = curl_init();
curl_setopt($s,CURLOPT_URL,$watson_url); 
curl_setopt($s,CURLOPT_HTTPHEADER,array($header_accept)); 
curl_setopt($s,CURLOPT_RETURNTRANSFER,true); 

// Calling the service
$result = curl_exec($s); 
$status = curl_getinfo($s,CURLINFO_HTTP_CODE); 
curl_close($s); 

if ($status != 200){
	throw new Exception("IBM Watson error : " . $status);
}

// Save the file
file_put_contents($download_file_path,$result);

// If MP3 format is requested
if ($format == "mp3"){

// encoding type
  $encoding = new Settings\Encoding\Preset();
  $encoding->setType(Settings\Encoding\Preset::TYPE_STANDARD);

// lame settings
  $settings = new Settings\Settings($encoding);

// lame wrapper
  $lame = new Lame('/usr/bin/lame', $settings);

  try {
      $lame->encode($download_file_path, $sound_file_path, function($inputfile, $outputfile) {
            unlink($inputfile);
        });
  } catch(\RuntimeException $e) {
      var_dump($e->getMessage());
  } 
}

$response = file_get_contents($sound_file_path);
header('Content-Disposition: attachment; filename="'.$id.'.'.$extension.'"');

echo $response;

