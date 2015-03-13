<?php
/** @var \App\Application $app */

// Main page
$app->get('/', '\\App\\Controller\\IndexController::actionIndex');

// Cities list
$app->get('/api/cities', '\\App\\Controller\\ApiController::actionCities');

// Get data for city
$app->get('/api/cities/{id}', '\\App\\Controller\\ApiController::actionCity')
    ->assert('id', '[a-z_]+');

// Data fetch trigger
$app->get('/api/fetch', '\\App\\Controller\\ApiController::actionFetch');