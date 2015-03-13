<?php return array(
    'app'     => array(
        'name'       => 'weathermap',
        'timezone'   => 'UTC',
        'configs'    => ROOTDIR . '/var/config',
        'apiUrl'     => 'http://pogoda.ngs.ru/json/',
        'fetchToken' => null,
        'cities'     => array()
    ),
    'silex'   => array(
        'locale' => 'ru'
    ),
    'monolog' => array(
        'maxFiles' => 2,
        'dir'      => ROOTDIR . '/var/log'
    ),
    'twig'    => array(
        'cache' => false
    )
);