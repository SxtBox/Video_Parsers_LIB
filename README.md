```markdown
# Video Parsers LIBs

Library to load and embed a video based on a YouTube, Vimeo or Wistia URL.

## Install
composer requiresxtbox/video-parsers-lib
composer dump-autoload

> NOTE: Ensure `composer.json` PSR-4 autoload maps `Video_Parsers\\` => `src/` (this package expects namespaces `Video_Parsers` in source files).

## Quick usage

```php
<?php
require 'vendor/autoload.php';

// load video (automatically returns Youtube/Vimeo/Wistia subclass)
$video = \Video_Parsers\Video::load('https://youtu.be/iUhGMLFo_dw');

// get metadata
echo 'Type: ' . $video->type() . PHP_EOL;
echo 'ID: ' . $video->id() . PHP_EOL;
echo 'Title: ' . $video->title() . PHP_EOL;
echo 'Thumbnail URL: ' . $video->thumbnail() . PHP_EOL;

// get embed HTML (height, width)
echo $video->html(360, 640);

// download thumbnail
$downloader = $video->download_thumbnail('thumb.jpg');
if ($downloader) {
  echo "Thumbnail downloaded\n";
} else {
  echo "Failed to download thumbnail\n";
}
```

## CLI

See `Examples/video_info.php.php` for a small example to print video info from the CLI.

## Notes

- The library uses `file_get_contents` to call oEmbed endpoints; for production consider using a HTTP client with timeouts and better error handling.
- Make sure `allow_url_fopen` is enabled or replace `file_get_contents` with a proper HTTP client.
