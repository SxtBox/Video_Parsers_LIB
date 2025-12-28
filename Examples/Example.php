<?php
require __DIR__ . "/../vendor/autoload.php";

use Video_Parsers\Video;

$url = "https://www.youtube.com/watch?v=iUhGMLFo_dw";
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
try {
    $video = Video::load($url);

    // përdor metodat publike, jo properties të mbrojtura
    echo "URL: " . $video->url() . PHP_EOL;
    echo "Type: " . $video->type() . PHP_EOL;
    echo "ID: " . $video->id() . PHP_EOL;
    echo "Title: " . $video->title() . PHP_EOL;
    echo "Thumbnail URL: " . $video->thumbnail() . PHP_EOL;

    // provoni të shkarkoni thumbnail (kthen objekt ose null)
    $downloader = $video->download_thumbnail('thumb.jpg');

    if ($downloader !== null && method_exists($downloader, 'isDownloaded') && $downloader->isDownloaded()) {
        echo "Thumbnail saved to: " . $downloader->getFilePath() . PHP_EOL;
    } elseif ($downloader !== null && method_exists($downloader, 'getFilePath')) {
        // nëse download() kthen path direkt, getFilePath() do të shfaqet
        echo "Thumbnail path: " . $downloader->getFilePath() . PHP_EOL;
    } else {
        echo "Thumbnail download failed or not available." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}