<?php
// Composer autoloader
require ROOTDIR . '/vendor/autoload.php';

// Application namespace autoloading
$loader = new \Composer\Autoload\ClassLoader();
$loader->addPsr4('App\\', ROOTDIR . '/app/src');
$loader->register();

// Application init
$app = new \App\Application(array('debug' => getenv('PRODUCTION') === false));

require 'services.php';
require 'routes.php';

$app->run();