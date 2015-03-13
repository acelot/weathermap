<?php

namespace App\Controller;

use App\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController
{
    public function actionCities(Application $app)
    {
        /** @var \MongoDB $db */
        $db = $app['mongo.db'];
        $cursor = $db->selectCollection('cities')->find();

        return new JsonResponse(iterator_to_array($cursor));
    }

    public function actionCity(Application $app, $id)
    {
        /** @var \MongoDB $db */
        $db = $app['mongo.db'];

        // Querying data
        $cursor = $db->selectCollection('data')
            ->find(array('city' => $id))
            ->fields(array('_id' => false, 'temp' => true, 'fetched' => true))
            ->sort(array('fetched' => -1))
            ->limit(150);

        return new JsonResponse(iterator_to_array($cursor, false));
    }

    public function actionFetch(Application $app, Request $request)
    {
        $config = $app['config'];

        // Check token
        if ($request->get('token') != $config['app']['fetchToken']) {
            return new Response('Token required', Response::HTTP_FORBIDDEN);
        }

        /** @var \MongoDB $db */
        $db = $app['mongo.db'];

        // Fetching data from external API
        $response = self::jsonRpcRequest($config['app']['apiUrl'], 'getForecasts', array(
            'name'   => array('current'),
            'cities' => $db->selectCollection('cities')->distinct('_id')
        ));

        if ($response !== null) {
            $app->logDump($response);

            $batch = array();
            $timestamp = time();

            // Preparing batch insert data
            foreach ($response['result'] as $key => $value) {
                // Replace commas to dots (for example, -1,2 => -1.2)
                $dotTemp = str_replace(',', '.', $value['current']['temp_current_c']);

                $batch[] = array(
                    'city'    => $key,
                    'temp'    => round(floatval($dotTemp), 1),
                    'date'    => new \MongoDate(strtotime($value['current']['date'])),
                    'fetched' => new \MongoDate($timestamp)
                );
            }

            // Selecting collection and inserting data
            $db->selectCollection('data')->batchInsert($batch);
        }

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    private static function jsonRpcRequest($url, $method, $params = array(), $id = null)
    {
        $data = array(
            'jsonrpc' => '2.0',
            'method'  => $method,
            'params'  => $params,
            'id'      => $id
        );

        $context = stream_context_create(array(
            'http' => array(
                'header'  => 'Content-type: application/json',
                'method'  => Request::METHOD_POST,
                'content' => json_encode($data)
            )
        ));

        $result = file_get_contents($url, false, $context);

        return json_decode($result, true);
    }
}