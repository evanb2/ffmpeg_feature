<?php
require __DIR__ . '/vendor/autoload.php';


class Converter
{

    public function probeAudioFile($input) {
        print_r($input);
        $ffprobe = FFMpeg\FFProbe::create();
        $output = array();
        $format = $ffprobe->format($input)->get('format_name');
        $channels = $ffprobe->streams($input)->audios()->first()->get('channels');
        $bits = $ffprobe->streams($input)->audios()->first()->get('bits_per_sample');
        $sample_rate = $ffprobe->streams($input)->audios()->first()->get('sample_rate');
        array_push($output, $format, $channels, $bits, $sample_rate);
        return $output;
    }

    public function convertAudioFile($input) {
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

}
