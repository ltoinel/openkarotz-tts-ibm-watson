<?php

/**
 * Call the IBM Watson TTS API to convert a text into a sound.
 *
 * @param $text
 *   The text to synthesis.
 * @param string $format
 *   The sound file to ask to IBM Watson.
 * @return string
 *   The file path of the sound saved.
 * @throws Exception
 */
function voiceSynthesis($text, $format){

    // Checking the format.
    if ($format == "ogg") {
        $header_accept = "Accept: audio/ogg;codecs=opus";
        $param_accept = NULL;
        $extension = "ogg";

    } else if ($format == "wave") {
        $header_accept = "Accept: audio/wav";
        $param_accept = "&accept=audio%2Fwav";
        $extension = "wav";
    }

    // Generate an ID.
    $id = md5($text);
    $download_file_path = "./tmp/$id.$extension";

    // Build the Watson URL.
    $watson_url = "https://watson-api-explorer.mybluemix.net/text-to-speech/api/v1/synthesize?voice=fr-FR_ReneeVoice&text=" . urlencode($text) . $param_accept;

    // Init curl.
    $s = curl_init();
    curl_setopt($s, CURLOPT_URL, $watson_url);
    curl_setopt($s, CURLOPT_HTTPHEADER, array($header_accept));
    curl_setopt($s, CURLOPT_RETURNTRANSFER, true);

    // Calling the service.
    $result = curl_exec($s);
    $status = curl_getinfo($s, CURLINFO_HTTP_CODE);
    curl_close($s);

    // If we have an error.
    if ($status != 200) {
        throw new Exception("IBM Watson error : " . $status);
    }

    // Save the file.
    file_put_contents($download_file_path, $result);

    return $download_file_path;
}