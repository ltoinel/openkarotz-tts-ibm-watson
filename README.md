# Openkarotz TTS with IBM Watson

This is a bridge to let OpenKarotz use the IBM Watson TTS API.
The bride convert the wave files into mp3 supported by madplay player embedded into the Karotz.

### Deploy on a Webserver

* Deploy this project on a PHP server with Lame installed (HTTPS is not supported by the Karotz).
* You should normally able to use the service using the following URL : 

```
https://<your server>/tts.php?text=bonjour
```

### Fix your Karotz

* Telnet your Openkarotz (telnet on port 23 / login : karotz)
* Edit the file /www/cgi-bin/tts.inc with vi and add the following function :

```
function WatsonTTS {

TTS=$1
VOICE=$2
NOCACHE=$3

MD5FILE=$(echo "$TTS$VOICE" | md5sum | cut -d ' ' -f 1)
echo $( echo \"$TTS\" | UrlDecode)  > $CNF_DATADIR/Tmp/${MD5FILE}.txt
eval $( echo "curl -o ${CNF_DATADIR}/Tmp/${MD5FILE}.mp3 'http://<your server>/tts.php?format=mp3&text=${TTS}'" )  >>/dev/null 2>>/dev/null

Log "[TTS]"  "Playing sound ${MD5FILE}.mp3"
PlaySound $CNF_DATADIR/Tmp/${MD5FILE}.mp3

if [ "$NOCACHE" == "1" ]; then
rm -f $CNF_DATADIR/Tmp/${MD5FILE}.mp3   >>/dev/null 2>>/dev/null
rm -f $CNF_DATADIR/Tmp/${MD5FILE}.txt   >>/dev/null 2>>/dev/null
else
Log "[TTS]"  "Storing sound ${MD5FILE}.mp3 to cache"
fi

echo ${MD5FILE}
}
```


* Edit the file /www/cgi-bin/tts line 63  with vi and add the WatsonTTS entry :
```
case $TTS_ENGINE in
     1)  MP3_ID=$(GoogleTTS $TTS $VOICE $NO_CACHE $RAW_VOICE);;
     2)  MP3_ID=$(VioletTTS $TTS $VOICE $NO_CACHE $RAW_VOICE);;
     3)  MP3_ID=$(AcapelaTTS $TTS $VOICE $NO_CACHE $RAW_VOICE $MUTE);;
     *)  MP3_ID=$(WatsonTTS $TTS $VOICE $NO_CACHE $RAW_VOICE);;
```

Now you can test you the new IBM Watson voice on the Karotz ! 