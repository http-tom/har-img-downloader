<?php
namespace src\classes;

class DirectoryProcess {


    public static function listFiles($dir, $filetypes = [])
    {
        $files = scandir($dir);
        $filtered = [];

        foreach($files as $file)
        {
            $pi = pathinfo($file);
            if(!empty($filetypes) && !in_array($pi['extension'],$filetypes))
            {
                continue;
            }
            if($file != '.' && $file != '..')
            {
                $filtered[] = $file;
            }
        }

        return $filtered;
    }
}