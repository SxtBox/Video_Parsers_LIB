<?php
namespace Video_Parsers\Utils;

/**
 * Simple file downloader
 * - Uses cURL to download to a temp file
 * - Validates MIME type using finfo
 * - Validates extension against allowed list
 *
 * Usage:
 * $d = new FileDownloader($url, ['image/jpeg'], ['jpg','jpeg']);
 * $d->setName('thumb.jpg'); // optional, can include path or filename
 * $d->download();
 * $path = $d->getFilePath();
 */
class FileDownloader {
    private $url;
    private $allowedMimeTypes = [];
    private $allowedExtensions = [];
    private $name = null; // filename or path
    private $tmpFile = null;
    private $downloaded = false;
    private $finalPath = null;

    public function __construct($url, array $allowedMimeTypes = [], array $allowedExtensions = []) {
        $this->url = $url;
        $this->allowedMimeTypes = $allowedMimeTypes;
        $this->allowedExtensions = array_map('strtolower', $allowedExtensions);
    }

    /**
     * Set desired file name (filename or full path). If not set the file will be saved
     * in sys_get_temp_dir() with a generated name.
     *
     * @param string $name
     * @return $this
     */
    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    /**
     * Perform the download and validation.
     *
     * @throws \Exception on failure
     * @return string full path to saved file
     */
    public function download() {
        $ch = curl_init($this->url);
        if ($ch === false) {
            throw new \Exception('cURL initialization failed');
        }

        // create temp file
        $tmpFp = tmpfile();
        if ($tmpFp === false) {
            curl_close($ch);
            throw new \Exception('Failed to create temporary file');
        }
        $meta = stream_get_meta_data($tmpFp);
        $this->tmpFile = $meta['uri'];

        // follow redirects, set timeout, write to temp file
        curl_setopt($ch, CURLOPT_FILE, $tmpFp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Video_Parsers_LIB/1.0 (+https://github.com/SxtBox/Video_Parsers_LIB)');
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        // execute
        $ok = curl_exec($ch);
        if ($ok === false) {
            $err = curl_error($ch);
            curl_close($ch);
            fclose($tmpFp);
            @unlink($this->tmpFile);
            throw new \Exception('Download failed: ' . $err);
        }

        // get effective url and content-type
        $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        // validate mime type using finfo (more reliable)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($this->tmpFile);

        if (!empty($this->allowedMimeTypes) && !in_array($mime, $this->allowedMimeTypes, true)) {
            fclose($tmpFp);
            @unlink($this->tmpFile);
            throw new \Exception('Invalid MIME type: ' . $mime);
        }

        // determine extension: prefer provided name's extension, else infer from effective URL or mime
        $extension = null;
        if ($this->name) {
            $ext = pathinfo($this->name, PATHINFO_EXTENSION);
            if ($ext) {
                $extension = strtolower($ext);
            }
        }
        if (!$extension) {
            // try from effective url
            $path = parse_url($effectiveUrl, PHP_URL_PATH) ?: '';
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ($ext) {
                $extension = strtolower($ext);
            }
        }
        if (!$extension) {
            // infer from mime (basic map)
            $map = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
            ];
            if (isset($map[$mime])) {
                $extension = $map[$mime];
            }
        }

        if (empty($extension) || (!empty($this->allowedExtensions) && !in_array($extension, $this->allowedExtensions, true))) {
            fclose($tmpFp);
            @unlink($this->tmpFile);
            throw new \Exception('Invalid or disallowed file extension: ' . ($extension ?? 'unknown'));
        }

        // decide final filename/path
        if ($this->name) {
            $candidate = $this->name;
            // if name is a directory, append generated name
            if (is_dir($this->name)) {
                $candidate = rtrim($this->name, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . uniqid('thumb_', true) . '.' . $extension;
            } else {
                // ensure it has correct extension
                if (pathinfo($candidate, PATHINFO_EXTENSION) === '') {
                    $candidate .= '.' . $extension;
                }
            }
        } else {
            $candidate = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('thumb_', true) . '.' . $extension;
        }

        // move temp file to final path
        // close tmp file pointer before rename
        fclose($tmpFp);
        if (!@rename($this->tmpFile, $candidate)) {
            // try copy then unlink
            if (!@copy($this->tmpFile, $candidate)) {
                @unlink($this->tmpFile);
                throw new \Exception('Failed to move downloaded file to final destination');
            }
            @unlink($this->tmpFile);
        }

        $this->finalPath = $candidate;
        $this->downloaded = true;

        return $this->finalPath;
    }

    /**
     * Get full path to downloaded file (after download)
     * @return string|null
     */
    public function getFilePath() {
        return $this->finalPath;
    }

    /**
     * Get filename
     * @return string|null
     */
    public function getFilename() {
        return $this->finalPath ? basename($this->finalPath) : null;
    }

    /**
     * Whether download has completed
     * @return bool
     */
    public function isDownloaded() {
        return $this->downloaded;
    }
}