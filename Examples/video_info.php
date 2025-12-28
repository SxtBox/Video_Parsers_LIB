#!/usr/bin/env php
<?php
//require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/../vendor/autoload.php";

use Video_Parsers\Video;

if ($argc < 2) {
    echo "Usage: video_info.php <video-url>\n";
    exit(1);
}

$url = $argv[1];

try {
    $video = Video::load($url);

    $data = [
        "url" => $video->url(),
        "type" => $video->type(),
        "id" => $video->id(),
        "title" => $video->title(),
        "description" => $video->description(),
        "thumbnail" => $video->thumbnail(),
    ];

    echo json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Exception $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . PHP_EOL);
    exit(2);
}