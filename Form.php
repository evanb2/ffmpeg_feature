<?

    echo shell_exec("ffmpeg -i Test.mp3 -acodec pcm_u8 -ar 96 Test.wav");

?>
