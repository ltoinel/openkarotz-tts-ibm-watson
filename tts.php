<?php

/**
 *  TTS API for OpenKarotz using IBM Watson API.
 */

require 'vendor/autoload.php';

use Lame\Lame;
use Lame\Settings;


// Get parameters
$text = $_GET['text'];

if (empty($text)){
	throw new Exception("Missing 'text' argument");
}

$id = md5($text);
$wave_file_path = "./tmp/".$id.".wav";
$mp3_file_path = "./tmp/".$id.".mp3";

// Check if the file already exists
if (file_exists($mp3_file_path)){
 $response = file_get_contents($mp3_file_path);
 echo $response;
}

// IBM Watson API
$watson_url = "https://watson-api-explorer.mybluemix.net/text-to-speech/api/v1/synthesize?accept=audio%2Fwav&voice=fr-FR_ReneeVoice&text=".$text;

$s = curl_init();
curl_setopt($s,CURLOPT_URL,$watson_url); 
curl_setopt($s,CURLOPT_HTTPHEADER,array('Accept: audio/wav')); 
curl_setopt($s,CURLOPT_RETURNTRANSFER,true); 

// Calling the service
$result = curl_exec($s); 
$status = curl_getinfo($s,CURLINFO_HTTP_CODE); 
curl_close($s); 

if ($status != 200){
	throw new Exception("IBM Watson error : " . $status);
}

file_put_contents($wave_file_path,$result);

// encoding type
$encoding = new Settings\Encoding\Preset();
$encoding->setType(Settings\Encoding\Preset::TYPE_STANDARD);

// lame settings
$settings = new Settings\Settings($encoding);

// lame wrapper
$lame = new Lame('/usr/bin/lame', $settings);

try {
    $lame->encode($wave_file_path, $mp3_file_path, function($inputfile, $outputfile) {
            unlink($inputfile);
        });
} catch(\RuntimeException $e) {
    var_dump($e->getMessage());
} 

$response = file_get_contents($mp3_file_path);

echo $response;

