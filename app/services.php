<?php
/** @var \App\Application $app */

// Services
$app->register(new \App\Provider\ConfigServiceProvider());
$app->register(new \App\Provider\MongoDbServiceProvider());
$app->register(new \App\Provider\MonologServiceProvider());
$app->register(new \App\Provider\TwigServiceProvider());

// Error handler
\Symfony\Component\Debug\ErrorHandler::register();