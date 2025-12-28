<?php
namespace Video_Parsers\Video;

/**
 * Helper class to get Video information for a vimeo video.
 */
class Vimeo extends \Video_Parsers\Video {

    /**
     * Constructor
     * 
     * @param   string              Video url
     */
    public function __construct($url) {
        parent::__construct($url);
    }
    
    /**
 * Get Video thumbnail
 *
 * @return  string              Video thumbnail url
 */
    public function thumbnail() {
        // if the thumbnail is known, return it
        if (!is_null($this->thumbnail)) {
            return $this->thumbnail;
        }

        // get Video information
        $information = $this->videoApiInformation();

        // if there is no video image, return out
        if (!isset($information[0]) || (isset($information[0]) && !isset($information[0]['thumbnail_large']))) {
            return $this->thumbnail = '';
        }

        // done, set and return the thumbnail
        return $this->thumbnail = str_replace('https:', '', $information[0]['thumbnail_large']);
    }

    /**
     * Get Video title
     *
     * @return  string              Video title
     */
    public function title() {
        // if the title is known, return it
        if (!empty($this->title)) {
            return $this->title;
        }

        // get Video information
        $information = $this->videoApiInformation();

        // if there is no video title, return out
        if (!isset($information[0]) || (isset($information[0]) && !isset($information[0]['title']))) {
            return $this->title = '';
        }

        // done, set and return the title
        return $this->title = $information[0]['title'];
    }

    /**
     * Get Video description
     *
     * @return  string              Video description
     */
    public function description() {
        // if the description is known, return it
        if (!empty($this->description)) {
            return $this->description;
        }

        // get Video information
        $information = $this->videoApiInformation();

        // if there is no video description, return out
        if (!isset($information[0]) || (isset($information[0]) && !isset($information[0]['description']))) {
            return $this->description = '';
        }

        // done, set and return the description
        return $this->description = $information[0]['description'];
    }

    /**
     * Video embed code
     * 
     * @param   int             Video height
     * @param   int             Video width
     * @return  string          Video embed code
     */
    public function html($height, $width) {
        // create the vimeo iframe and return it
        return '<iframe id="' . $this->id() . '" class="vimeo-video-player" class="expand" height="'. $height . '" src="//player.vimeo.com/video/' . $this->id() . '?api=1&player_id=' . $this->id() . '" '
                . 'width="' . $width . '" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
    }

    /**
     * Helper function to parse an video information from URL
     * 
     * @param   string                URL to parse
     * @return  \Video_Parsers\Video         The instance of this, to make chaining possible
     */
    protected function parse($url) {
        // parse whenever we are dealing with vimeo
        if (preg_match('/vimeo/', $url) == 0) {
            // something went wrong, throw an exception
            throw new \Exception('Not a valid video');
        }
        
        // try getting the id
        if (preg_match('/^https?:\/\/(www\.)?vimeo\.com\/(clip\:)?(\d+).*$/', $url, $match) != 0) {
            $this->id = $match[3];
        } elseif (preg_match('/^https?:\/\/(www\.)?vimeo\.com\/video\/(clip\:)?(\d+).*$/', $url, $match) != 0) {
            $this->id = $match[3];
        }

        // set the type
        $this->type = 'vimeo';

        // done, return out
        return $this;
    }
    
    /**
     * Get VideoApiInformation
     * 
     * @return mixed                    Video information from Video api of the type
     */
    protected function videoApiInformation() {
        // if Video information has been set before, return it
        if (!is_null($this->videoInformation)) {
            return $this->videoInformation;
        }
        
        // done, return the vimeo information
        return $this->videoInformation = unserialize(@file_get_contents('https://vimeo.com/api/v2/video/' . $this->id() . '.php'));
    }
    
}
