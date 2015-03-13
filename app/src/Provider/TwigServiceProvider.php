<?php

namespace App\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class TwigServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['twig'] = $app->share(function ($app) {
            $environment = new \Twig_Environment(
                $app['twig.loader'],
                array_merge(array('debug' => $app['debug']), $app['config']['twig'])
            );

            // Extensions
            if ($app['debug']) {
                $environment->addExtension(new \Twig_Extension_Debug());
            }

            // Globals
            $environment->addGlobal('app', $app);

            return $environment;
        });

        $app['twig.loader'] = $app->share(function () {
            return new \Twig_Loader_Filesystem(ROOTDIR . '/app/views');
        });
    }

    public function boot(Application $app)
    {
        // noop
    }
}