<?php
// examples/embed.php
// Shton embed HTML (iframe) nga objekti Video

//require __DIR__ . "/vendor/autoload.php";
require __DIR__ . "/../vendor/autoload.php";

$url = "https://vimeo.com/308734134"; // shembull Vimeo

try {
    $video = \Video_Parsers\Video::load($url);

    // shumica e implementimeve në këtë lib japin metodën html($height, $width)
    $embed = $video->html(360, 640);

    // printo kodën embed (mund ta vendosësh brenda një template HTML)
    echo $embed;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}