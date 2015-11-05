<?php
    date_default_timezone_set('America/Los_Angeles');
    require __DIR__ . '/vendor/autoload.php';
    // include 'ConverterClass.php';
    function probeAudioFile($input) {
        $file_path = dirname(__FILE__) . "/audio_files/" . $input;
        // print_r($file_path);
        $ffprobe = FFMpeg\FFProbe::create();
        $output = array();
        $format = $ffprobe->format($file_path)->get('format_name');
        $channels = $ffprobe->streams($file_path)->audios()->first()->get('channels');
        $bits = $ffprobe->streams($file_path)->audios()->first()->get('bits_per_sample');
        $sample_rate = $ffprobe->streams($file_path)->audios()->first()->get('sample_rate');
        array_push($output, $format, $channels, $bits, $sample_rate);
        return $output;
    }

    function convertAudioFile($input) {
        $file_path = dirname(__FILE__) . "/audio_files/" . $input;
        $ffmpeg = FFMpeg\FFMpeg::create();
        $audio = $ffmpeg->open($file_path);
        $format = new FFMpeg\Format\Audio\Wav();
        $format->on('progress', function ($audio, $format, $percentage) {
            echo "$percentage % transcoded";
        });
        $format->setAudioChannels(2);
        $audio->filters()->resample(96000);
        $audio->save($format, 'Output_file2.wav');
    }

    $audio_file = $_POST["audio_file"];

    $probe_input = probeAudioFile($audio_file);

    if ($probe_input[0] == "wav" && $probe_input[1] == 2 && $probe_input[2] == 24 && $probe_input[3] == 96000) {
        print_r("Nothing to do here.");
    } else {
        $converted_file = convertAudioFile($audio_file);
        // print_r($converted_file);
    }
        $probe_output = probeAudioFile($converted_file);
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
        <ul>
            <li>Format: <?php echo $probe_input[0] ?></li>
            <li>Channels: <?php echo $probe_input[1] ?></li>
            <li>Bits: <?php echo $probe_input[2] ?></li>
            <li>Sample Rate: <?php echo $probe_input[3] ?></li>
        </ul>
        <hr>
        <h4>Output File Info</h4>
        <ul>
            <li>Format: <?php echo $probe_output[0] ?></li>
            <li>Channels: <?php echo $probe_output[1] ?></li>
            <li>Bits: <?php echo $probe_output[2] ?></li>
            <li>Sample Rate: <?php echo $probe_output[3] ?></li>
        </ul>
        <hr>
        <h4>Format:</h4>
        <p><?php print_r(FFMpeg\FFProbe::create()->format($audio_file)); ?></p>
        <hr>
        <h4>Streams:</h4>
        <p><?php print_r(FFMpeg\FFProbe::create()->streams($audio_file)); ?></p>

    </body>
</html>
