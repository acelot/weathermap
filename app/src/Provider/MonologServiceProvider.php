<?php

namespace App\Provider;

use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Monolog\Processor\PsrLogMessageProcessor;
use Silex\Application;
use Silex\EventListener\LogListener;
use Silex\ServiceProviderInterface;

class MonologServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['logger'] = function () use ($app) {
            return $app['monolog'];
        };

        $app['monolog'] = $app->share(function ($app) {
            $logger = new Logger($app['config']['app']['name']);

            if ($app['debug']) {
                $logger->pushHandler($app['monolog.handler']);
            } else {
                $logger->pushHandler(new FingersCrossedHandler($app['monolog.handler']));
            }

            $logger->pushProcessor($app['monolog.processor']);

            return $logger;
        });

        $app['monolog.handler'] = $app->share(function ($app) {
            $fileName = sprintf('%s/%s-%s.log',
                $app['config']['monolog']['dir'],
                $app['config']['app']['name'],
                $app['debug'] ? 'debug' : 'production'
            );

            return new RotatingFileHandler($fileName, $app['config']['monolog']['maxFiles']);
        });

        $app['monolog.processor'] = $app->share(function () {
            return new PsrLogMessageProcessor();
        });

        $app['monolog.listener'] = $app->share(function ($app) {
            return new LogListener($app['logger']);
        });
    }

    public function boot(Application $app)
    {
        $app['dispatcher']->addSubscriber($app['monolog.listener']);
    }
}