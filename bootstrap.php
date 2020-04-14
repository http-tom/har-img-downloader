<?php
if(function_exists('apache_setenv'))
{
    apache_setenv('no-gzip', '1');
}
ini_set('max_execution_time', 0);
ini_set('implicit_flush', 1);
ob_implicit_flush(1);


class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            if (file_exists($file)) {
                require_once($file);
                return true;
            }
            return false;
        });
    }
}

Autoloader::register();
