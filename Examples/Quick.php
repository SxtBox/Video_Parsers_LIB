<?php
//require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/../vendor/autoload.php";

use Video_Parsers\Video;

// Example URLs:
// $url = "https://youtu.be/iUhGMLFo_dw";
// $url = "https://vimeo.com/308734134";
// $url = "https://fast.wistia.com/medias/abcd1234";

$url = $argv[1] ?? "https://youtu.be/iUhGMLFo_dw";

try {
    $video = Video::load($url);

    echo "URL: " . $video->url() . PHP_EOL;
    echo "Type: " . $video->type() . PHP_EOL;
    echo "ID: " . $video->id() . PHP_EOL;
    echo "Title: " . $video->title() . PHP_EOL;
    echo "Description: " . $video->description() . PHP_EOL;
    echo "Thumbnail: " . $video->thumbnail() . PHP_EOL;
    echo "Embed HTML (360x640):" . PHP_EOL;
    echo $video->html(360, 640) . PHP_EOL;

    // Try downloading thumbnail (if th/filedownloader available)
    $downloader = $video->download_thumbnail(basename($video->id()) . ".jpg");
    if ($downloader) {
        echo "Thumbnail download attempted." . PHP_EOL;
    } else {
        echo "Thumbnail download failed or not available." . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}