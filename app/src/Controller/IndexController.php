<?php

namespace App\Controller;

use App\Application;

class IndexController
{
    public function actionIndex(Application $app)
    {
        return $app->render('index/index.twig');
    }
}