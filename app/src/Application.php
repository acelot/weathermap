<?php

namespace App;

use App\Application\LoggerTrait;
use Silex\Application\TwigTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Application extends \Silex\Application
{
    use LoggerTrait;
    use TwigTrait;
}