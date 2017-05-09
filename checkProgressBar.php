<?php

$fileName = 'progressBar/progressBar' . $_GET['sessid'] . '.txt';
$progressStatus = 0;

if (file_exists($fileName)) {
    $fileProgressBar = fopen($fileName, 'r');

    $progressStatus = fgets($fileProgressBar);

    fclose($fileProgressBar);
}

echo $progressStatus;
