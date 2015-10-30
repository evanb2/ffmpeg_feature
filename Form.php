<?php
    date_default_timezone_set('America/Los_Angeles');
    require __DIR__ . '/vendor/autoload.php';

    $audio_file = $_POST["audio_file"];

    function probeAudioFile($input) {
        $ffprobe = FFMpeg\FFProbe::create();
        $output = array();
        $format = $ffprobe->format($input)->get('format_name');
        $channels = $ffprobe->streams($input)->audios()->first()->get('channels');
        $codec_name = $ffprobe->streams($input)->audios()->first()->get('bits_per_sample');
        $sample_rate = $ffprobe->streams($input)->audios()->first()->get('sample_rate');
        array_push($output, $format, $channels, $codec_name, $sample_rate);
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
        <hr>
        <ul><?php foreach ($probe as $property) {
                echo "<li>$property</li>";
            } ?>
        </ul>
        <hr>
        <h4>Format:</h4>
        <p><?php print_r(FFMpeg\FFProbe::create()->format($audio_file)); ?></p>
        <hr>
        <h4>Streams:</h4>
        <p><?php print_r(FFMpeg\FFProbe::create()->streams($audio_file)); ?></p>

    </body>
</html>
