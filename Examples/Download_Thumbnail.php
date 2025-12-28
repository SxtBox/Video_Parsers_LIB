<?php
// examples/download_thumb.php
// Përpiqet të shkarkojë thumbnail-in dhe ta ruajë me emër të dhënë

//require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/../vendor/autoload.php";

$url = "https://home.wistia.com/medias/e4a27b971d"; // zëvendëso me Wistia/URL të vërtetë
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
try {
    $video = \Video_Parsers\Video::load($url);

    // seto emrin e skedarit (pa extension) nëse dëshiron
    $downloader = $video->download_thumbnail("my_video_thumb");

    if ($downloader !== null) {
        echo "Thumbnail shkarkuar: " . $downloader->getPath() . PHP_EOL;
    } else {
        echo "Nuk u mund të shkarkohej thumbnail." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}