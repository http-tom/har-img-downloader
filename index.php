<?php
require_once('bootstrap.php');

use src\classes\HarProcessor;
use src\classes\DirectoryProcess;

// load *.har files from har/ directory
$files = DirectoryProcess::listFiles(dirname(__FILE__).DIRECTORY_SEPARATOR.'har/', ['har']);

$p = new HarProcessor($files, dirname(__FILE__), 'images', HarProcessor::NO_OVERWRITE_FILES);
