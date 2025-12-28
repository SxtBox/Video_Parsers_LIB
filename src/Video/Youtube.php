<?php
namespace Video_Parsers\Video;

/**
 * Helper class to get the youtube video information for a video
 */
class Youtube extends \Video_Parsers\Video {

    /**
     * Whether video is low res or not
     *
     * @var boolean
     */
    private $lowRes = false;

    /**
     * Constructor
     *
     * @param string Video url
     */
    public function __construct($url) {
        parent::__construct($url);
    }

    /**
     * Get video thumbnail
     *
     * @return string Video thumbnail url
     */
    public function thumbnail() {
        // if the thumbnail is known, return it
        if (!is_null($this->thumbnail)) {
            return $this->thumbnail;
        }

        // done, add and return the thumbnail
        return $this->thumbnail = 'https//img.youtube.com/vi/' . $this->id() . '/maxresdefault.jpg';
    }

    /**
     * Get video title
     *
     * @return string video title
     */
    public function title() {
        // if the title is known, return it
        if (!empty($this->title)) {
            return $this->title;
        }

        // get Video information
        $information = $this->videoApiInformation();

        // if there is no video title, return out
        if (!isset($information->title)) {
            return $this->title = '';
        }

        // done, set and return the title
        return $this->title = $information->title;
    }

    /**
     * video embed code
     *
     * @param   int Video height
     * @param   int Video width
     * @return  string Video embed code
     */
    public function html($height, $width) {
        // set the element that the player element will be linked to
        $html = '<div class="ytplayer" id="' . $this->id() . '" data-width="' . $width . '" data-height="' . $height . '" data-video-id="' . $this->id() . '"></div>';

        // return the markup
        return $html;
    }

    /**
     * Function to download the thumbnail file
     *
     * @param   string|null Possible name, to use to store the file
     * @return  \Th\FileDownloader The file download instance
     */
    public function download_thumbnail($name = null) {
        // call the parent
        $downloader = parent::download_thumbnail($name);

        // if there is a downloader, return it
        if (!is_null($downloader)) {
            return $downloader;
        }

        // no downloader available, so set a low res thumbnail to make the
        // thumb available still
        $this->thumbnail = 'https//img.youtube.com/vi/' . $this->id() . '/0.jpg';

        // set Video to be low res
        $this->lowRes = true;

        // done, return the downloader
        return parent::download_thumbnail($name);
    }

    /**
     * Return whether Video is low res or not
     *
     * @return boolean Whether Video is low res or not
     */
    public function isLowRes() {
        return $this->lowRes;
    }

    /**
     * Helper function to parse an video information from URL
     *
     * @param   string URL to parse
     * @return  \Video_Parsers\Video The instance of this, to make chaining possible
     */
    protected function parse($url) {
        // if something went wrong, throw an error
        if (preg_match('/youtu\.?be/', $url) == 0) {
            throw new \Exception('Not a valid video');
        }

        // filter out the id
        if (preg_match('%(?:youtube\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            $this->id = $match[1];
        }

        // set the type
        $this->type = 'youtube';

        // done, return out
        return $this;
    }

    /**
     * Get VideoApiInformation
     *
     * @return mixed Video information from Video api of the type
     */
    protected function videoApiInformation() {
        // if Video information has been set before, return it
        if (!is_null($this->videoInformation)) {
            return $this->videoInformation;
        }

        // done, return the information
        return $this->videoInformation = json_decode(@file_get_contents('https://www.youtube.com/oembed?url=' . urlencode($this->url()) . '&format=json'));
    }

}
