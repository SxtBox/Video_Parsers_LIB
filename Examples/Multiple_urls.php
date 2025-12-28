<?php
// examples/multiple_urls.php
// Për çdo URL tregon tipin, id-në dhe një preview thumbnail

require __DIR__ . "/../vendor/autoload.php";

$urls = [
    "https://www.youtube.com/watch?v=iUhGMLFo_dw",
    "https://vimeo.com/308734134",
    "https://home.wistia.com/medias/e4a27b971d" // shembull
];

header("Access-Control-Allow-Origin: *");
header("Content-type: application/json");

foreach ($urls as $u) {
    try {
        $v = \Video_Parsers\Video::load($u);
        printf(
            "URL -> %s Provider -> %s ID -> %s Thumbnail -> %s\n",
            $u . PHP_EOL,
            $v->type() . PHP_EOL,
            $v->id() . PHP_EOL,
            $v->thumbnail() . PHP_EOL
        );
    } catch (Exception $e) {
        echo "Could not parse $u: " . $e->getMessage() . PHP_EOL;
    }
}