<?php
//phpinfo();
use app\Application;
use app\Loader;

require_once '../application/Loader.php';
spl_autoload_register([new Loader(), 'loadClass']);
Application::start();