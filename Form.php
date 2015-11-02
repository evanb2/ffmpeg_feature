<?php
    date_default_timezone_set('America/Los_Angeles');
    require __DIR__ . '/vendor/autoload.php';

    $audio_file = $_POST["audio_file"];

    function probeAudioFile($input) {
        // $input = "input/" . $input_file;
        $ffprobe = FFMpeg\FFProbe::create();
        $output = array();
        $format = $ffprobe->format($input)->get('format_name');
        $channels = $ffprobe->streams($input)->audios()->first()->get('channels');
        $bits = $ffprobe->streams($input)->audios()->first()->get('bits_per_sample');
        $sample_rate = $ffprobe->streams($input)->audios()->first()->get('sample_rate');
        array_push($output, $format, $channels, $bits, $sample_rate);
        return $output;
    }

    function convertAudioFile($input) {
        $ffmpeg = FFMpeg\FFMpeg::create();

        $audio = $ffmpeg->open($input);
        $format = new FFMpeg\Format\Audio\Wav();
        $format->on('progress', function ($audio, $format, $percentage) {
            echo "$percentage % transcoded";
        });

        $format->setAudioChannels(2);
        $audio->filters()->resample(96000);

        $audio->save($format, 'output/Output_file.wav');
    }

    $probe_input = probeAudioFile($audio_file);

    if ($probe_input[0] == "wav" && $probe_input[1] == 2 && $probe_input[2] == 24 && $probe_input[3] == 96000) {
        print_r("Nothing to do here.");
    } else {
        convertAudioFile($audio_file);
    }

    $probe_output = probeAudioFile("output/Output_file.wav");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Result</title>
    </head>
    <body>
        <h4>Input File Info</h4>
        <hr>
        <ul><?php foreach ($probe_input as $input_property) {
                echo "<li>$input_property</li>";
            } ?>
        </ul>
        <hr>
        <h4>Output File Info</h4>
        <ul><?php foreach ($probe_output as $output_property) {
                echo "<li>$output_property</li>";
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
