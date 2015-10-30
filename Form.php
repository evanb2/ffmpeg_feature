<?php
    date_default_timezone_set('America/Los_Angeles');
    require __DIR__ . '/vendor/autoload.php';

    $audio_file = $_POST["audio_file"];

    function probeAudioFile($input) {
        $ffprobe = FFMpeg\FFProbe::create();
        $output = array();
        $channels = $ffprobe->streams($input)->audios()->first()->get('channels');
        $codec_name = $ffprobe->streams($input)->audios()->first()->get('codec_name');
        array_push($output, $channels, $codec_name);
        return $output;
    }

    function convertAudioFile($input) {
        $ffmpeg = FFMpeg\FFMpeg::create();

        $audio = $ffmpeg->open($input);
        $format = new FFMpeg\Format\Audio\Wav();
        $format->on('progress', function ($audio, $format, $percentage) {
            echo "$percentage % transcoded";
        });

        $format
            ->setAudioChannels(2)
            ->setAudioKiloBitrate(256);

        $audio->save($format, 'output/Test_track4.wav');

    }

    $probe = probeAudioFile($audio_file);

    convertAudioFile($audio_file);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Result</title>
    </head>
    <body>
        <h4>Success</h4>
        <ul><?php foreach ($probe as $property) {
            echo "<li>$property</li>";
        } ?></ul>
    </body>
</html>
