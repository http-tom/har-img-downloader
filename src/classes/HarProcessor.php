<?php
namespace src\classes;

class HarProcessor {

    const NO_OVERWRITE_FILES = 0;
    const OVERWRITE_FILES = 1;

    private $basePath = '';
    private $_files = [];
    private $_images = [];
    private $overwriteMode = 1;

    /**
     * @param array $files array of har files to process
     * @param string $basePath Base path that is used, har files should be stored in here in a folder called har
     * @param string $imgFolder
     * @param int $overwriteMode HarProcessor::NO_OVERWRITE_FILES | HarProcessor::OVERWRITE_FILES
     */
    public function __construct($files, $basePath, $imgFolder = 'img', $overwriteMode = 1)
    {
        $this->basePath = $basePath;
        $this->_files = $files;
        $this->overwriteMode = $overwriteMode;

        $filesProcessed = 0;
        foreach($this->_files as $file)
        {
            $this->flush("Loading file {$file}");
            $this->processFile('har'.DIRECTORY_SEPARATOR.$file);
            $count = $this->getImages($imgFolder, $file);
            $this->flush("Processed {$count} files for {$file}");
            $filesProcessed += $count;
        }
        $this->flush("Finished. Processed a total of {$filesProcessed} images in " . count($this->_files) . " files");
    }

    /**
     * Outputs current state to browser, this could write to a log or whatever, if overridden
     * @param string $msg
     */
    protected function flush($msg)
    {
        echo str_pad(nl2br(date('Y-m-d H:i:s').'] '. $msg."\n"), 1024);
        ob_flush();
        flush();
    }

    /**
     * @param string $imgPath Base image path for the images to be stored
     * @param string $folder Images will be stored in a folder with the same name as the file being processed
     */
    protected function getImages($imgPath, $folder)
    {
        try
        {
            if(file_exists($this->basePath.DIRECTORY_SEPARATOR.$imgPath) == false)
            {
                $path = $this->basePath.DIRECTORY_SEPARATOR.$imgPath;
                $success = mkdir($path);
                if(!$success)
                {
                    throw new \Exception("Could not create {$path}");
                }
            }
            if(file_exists($this->basePath.DIRECTORY_SEPARATOR.$imgPath.DIRECTORY_SEPARATOR.$folder) == false)
            {
                $path = $this->basePath.DIRECTORY_SEPARATOR.$imgPath.DIRECTORY_SEPARATOR.$folder;
                $success = mkdir($path);
                if(!$success)
                {
                    throw new \Exception("Could not create {$path}");
                }
            }
            $dir = $this->basePath.DIRECTORY_SEPARATOR.$imgPath.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR;
            $dir = str_replace(DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR,DIRECTORY_SEPARATOR,$dir);
            if(file_exists($dir) === false)
            {
                $path = $dir;
                $success = mkdir($path);
                if(!$success)
                {
                    throw new \Exception("Could not create {$path}");
                }
            }
        } catch(\Exception $e) {
            die("Error creating locations: " . $e->getMessage());
            return false;
        }

        
        $count = 0;
        foreach($this->_images as $img)
        {
            $name = basename($img['url']);

            if(self::NO_OVERWRITE_FILES === $this->overwriteMode) {
                if(file_exists($dir.$name))
                {
                    continue;
                }
            }

            $file = file_get_contents($img['url']);
            file_put_contents($dir.$name, $file);
            $count++;
            set_time_limit(10);
        }
        return $count;
    }

    /**
     * Processes .har file, gets all image responses and stores them
     * @param String $file Filename of file to read
     */
    protected function processFile($file)
    {
        $fn = $this->basePath.DIRECTORY_SEPARATOR.$file;
        $json = json_decode(file_get_contents($fn),true);
        $this->_images = [];
        foreach($json['log']['entries'] as $k => $j)
        {
            if(in_array($j['response']['content']['mimeType'], [
                'image/png',
                'image/jpeg',
            ]))
            {
                $this->_images[] = [
                    'url' => $j['request']['url'],
                    'mime' => $j['response']['content']['mimeType'],
                    'encoding' => $j['response']['content']['encoding'],
                    'size' => $j['response']['content']['size'],
                    'content' => $j['response']['content']['text'],
                ];
            }
        }
    }

}
