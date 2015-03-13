<?php

namespace App\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['config'] = $app->share(function ($app) {
            // Default configuration file
            $default = require ROOTDIR . '/app/config.php';

            // Environment configuration file
            $path = sprintf('%s/%s.php', $default['app']['configs'], $app['debug'] ? 'debug' : 'production');
            $environment = file_exists($path) ? require $path : array();

            return array_replace_recursive($default, $environment);
        });
    }

    public function boot(Application $app)
    {
        // Merging Silex options
        foreach ($app['config']['silex'] as $key => $value) {
            $app[$key] = $value;
        }
    }
}