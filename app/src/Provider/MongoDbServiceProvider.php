<?php

namespace App\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class MongoDbServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['mongo.db'] = $app->share(function ($app) {
            /** @var \MongoClient $mongo */
            $mongo = $app['mongo'];
            $db = $mongo->selectDB($app['config']['db']['name']);

            return $db;
        });

        $app['mongo'] = $app->share(function ($app) {
            $config = $app['config']['db'];

            $connectionString = sprintf('mongodb://%s:%s@%s:%d/%s',
                $config['user'],
                $config['pass'],
                $config['host'],
                $config['port'],
                $config['name']
            );

            $mongo = new \MongoClient($connectionString);

            return $mongo;
        });
    }

    public function boot(Application $app)
    {
        // noop
    }
}