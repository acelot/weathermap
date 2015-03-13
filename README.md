# Weather map

Simple weather application. This app using `http://pogoda.ngs.ru/json` service as data source.

![weathermap](https://cloud.githubusercontent.com/assets/1065215/6632192/9652bc30-c975-11e4-8888-8a5c464a4be8.png)

## Requirements

- PHP >= 5.4
- MongoDB and PHP extension
- curl (server package)
- Composer
- Bower

## How to setup

- Clone this repo
- Configure nginx
- Configure MongoDB
- Configure app
- Install dependencies
- Configure cron

## Nginx

```nginx
server {
    server_name weathermap.dev;
    root '/clone/path/public';
    index index.php;

    try_files $uri $uri/ /index.php?$args;

    location /index.php {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php5-fpm.sock;
    }
}
```

## MongoDB

- Create database `weathermap`
- Create 2 collections: `cities`, `data`
- Create user for this database
- Insert some cities into `cities` collection

```json
[
    { "_id" : "barnaul", "name" : "Барнаул",     "coords" : [ 53.3457, 83.7786 ] }
    { "_id" : "krsk",    "name" : "Красноярск",  "coords" : [ 56.0258, 92.9745 ] }
    { "_id" : "nsk",     "name" : "Новосибирск", "coords" : [ 55.0269, 82.9253 ] }
    { "_id" : "tomsk",   "name" : "Томск",       "coords" : [ 56.4872, 84.9553 ] }
]
```

## App

Create some directories:

```shell
cd /clone/path
mkdir -p var/{config,log,cache}
chmod 775 var/log/ var/cache/
```

Create `/clone/path/var/config/debug.php` config file:

```php
<?php return array(
    'app' => array(
        'fetchToken' => 'mysupertoken'
    ),
    'db'  => array(
        'host' => '127.0.0.1',
        'port' => 27017,
        'name' => 'weathermap',
        'user' => 'weathermap',
        'pass' => '123456'
    )
);
```

## Dependencies

Run following commands in `/clone/path` directory:

```shell
composer install
bower install
```

## Cron

Schedule the data fetcher script:

```cron
*/15 * * * * curl -s http://weathermap.dev/api/fetch?token=mysupertoken
```

## License

The MIT License (MIT)

Copyright (c) 2015 acelot

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.