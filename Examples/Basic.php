<?php
// examples/basic.php
// Shembull i thjeshtë: ngarkon një URL video dhe printon informacionin

//require __DIR__ . "/vendor/autoload.php"; // nëse ke instaluar përmes composer
require __DIR__ . "/../vendor/autoload.php";

$url = "https://www.youtube.com/watch?v=iUhGMLFo_dw"; // zëvendëso me URL-në tënde
header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");
try {
    $video = \Video_Parsers\Video::load($url);

    echo "Type: " . $video->type() . PHP_EOL;
    echo "ID: " . $video->id() . PHP_EOL;
    echo "URL: " . $video->url() . PHP_EOL;
    echo "Title: " . $video->title() . PHP_EOL;
    echo "Description: " . $video->description() . PHP_EOL;
    echo "Thumbnail: " . $video->thumbnail() . PHP_EOL;
} catch (Exception $e) {
    echo "Error Loading Video: " . $e->getMessage() . PHP_EOL;
}