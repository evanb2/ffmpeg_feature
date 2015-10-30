<?php
    date_default_timezone_set('America/Los_Angeles');
    require __DIR__ . '/vendor/autoload.php';

    $ffmpeg = FFMpeg\FFMpeg::create();

    $audio = $ffmpeg->open('Test.mp3');
    $format = new FFMpeg\Format\Audio\Flac();
    $format->on('progress', function ($audio, $format, $percentage) {
        echo "$percentage % transcoded";
    });

    $format
        ->setAudioChannels(2)
        ->setAudioKiloBitrate(256);

    $audio->save($format, 'Test_track3.flac');
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Result</title>
    </head>
    <body>
        <h4>Success</h4>
    </body>
</html>
