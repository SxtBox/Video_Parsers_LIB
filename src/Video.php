<?php
namespace Video_Parsers;

/**
 * Helper class to get Video information for a video. Supported types are
 * vimeo, youtube and wistia
 */
abstract class Video {

    /**
     * Video url
     *  
     * @var string
     */
    protected $url;

    /**
     * Video type
     * 
     * @var string
     */
    protected $type;

    /**
     * Video identifier
     * 
     * @var string
     */
    protected $id;

    /**
     * Video thumbnail url
     * 
     * @var string
     */
    protected $thumbnail = null;

    /**
     * Video title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Video description
     *
     * @var string
     */
    protected $description = '';
    
    /**
     * Video information from the api
     * 
     * @var mixed
     */
    protected $videoInformation = null;
    
    /**
     * Return Video class object for URL
     * 
     * @param   string Video url
     * @return  \Video_Parsers\Video Video object
     * 
     * @throws Exception Whenever there is no supported type
     */
    public static function load($url) {
        return self::parse_video($url);
    }
    
    /**
     * Get Video thumbnail
     * 
     * @return string Video thumbnail url
     */
    abstract public function thumbnail();
    /**
     * Video embed code
     * 
     * @param   int Video height
     * @param   int Video width
     * @return  string Video embed code
     */
    abstract public function html($height, $width);
    
    /**
     * Get Video Api Information
     * 
     * @return mixed Video information from Video api of the type
     */
    abstract protected function videoApiInformation();

    /**
     * Get Video url
     * 
     * @return string Video url
     */
    public function url() {
        return $this->url;
    }

    /**
     * Get Video type (vimea, youtube, wistia)
     * 
     * @return string Video type
     */
    public function type() {
        return $this->type;
    }

    /**
     * Get Video identifier
     * 
     * @return string Video identifier 
     */
    public function id() {
        return $this->id;
    }

    /**
     * Get the title
     *
     * @return string Video title
     */
    public function title() {
        return $this->title;
    }

    /**
     * Get the description
     *
     * @return string Video description
     */
    public function description() {
        return $this->description;
    }
    
    /**
     * Set Video thumbnail
     * 
     * @param   string Video thumbnail
     */
    public function setThumbnail($thumbnail) {
        $this->thumbnail = $thumbnail;
    }

public function download_thumbnail($name = null) {
    return $this->downloadThumb($name);
}

    /**
     * Function to download the thumbnail file
     *
     * @param   string|null Possible name, to use to store the file
     * @return  \Video_Parsers\Utils\FileDownloader|null The file download instance or null on failure
     */
    public function downloadThumb($name = null) {
        // create the path to the thumbnail file
        $file = 'https://' . str_replace(array('http://', 'https://', '//'), '', $this->thumbnail());
       //$file = $this->thumbnail();
        // set the allowed mime types
        $allowedMimeTypes = array('image/jpeg', 'image/png', 'image/gif'); 

        // set the allowed image extensions
        $allowedExtensions = array('jpg', 'jpeg', 'png', 'gif');

        // try downloading the file using internal downloader
        try {
            $downloader = new \Video_Parsers\Utils\FileDownloader($file, $allowedMimeTypes, $allowedExtensions);

            // set the name if a name was given
            if (!is_null($name)) {
                $downloader->setName($name);
            }

            $downloader->download();
        } catch (\Exception $exception) {
            return null;
        }
        
        // done, return the downloader
        return $downloader;
    }
    
    /**
     * Return whether Video is low res or not (by default this is always false)
     * 
     * @return boolean Whether Video is low res or not
     */
    public function isLowRes() {
        return false;
    }
    
    /**
     * Constructor
     * 
     * @param string Video url
     */
    protected function __construct($url) {
        // parse Video information
        $this->url = $url;

        // parse Video information
        $this->parse($url);
    }
    
    /**
     * Parse Video and return Video class object
     * 
     * @param   string Video url
     * @return  \Video_Parsers\Video Video object
     * 
     * @throws Exception Whenever there is no supported type
     */
    private static function parse_video($url) {
        // parse the id and type
        if (preg_match('/youtu\.?be/', $url) == 1) {
            return new \Video_Parsers\Video\Youtube($url);
        }
        
        // parse whenever we are dealing with vimeo
        if (preg_match('/vimeo/', $url) == 1) {
            return new \Video_Parsers\Video\Vimeo($url);
        } 
        
        // parse in case of wistia
        if (preg_match('/https?:\/\/(.+)?(wistia.com|wi.st)\/(medias|embed)\/.*/', $url) == 1) {
            return new \Video_Parsers\Video\Wistia($url);
        }
        
        // something went wrong, throw an exception
        throw new \Exception('Not a valid video');
    }

}