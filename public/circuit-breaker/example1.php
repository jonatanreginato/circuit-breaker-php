<?php

declare(strict_types=1);

$client = new GuzzleHttp\Client();

try {
    $response = $client->request('GET', 'https://dummyjson.com/user/1');
    echo "<pre>" . $response->getBody() . "</pre>";
} catch (GuzzleHttp\Exception\GuzzleException $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}
