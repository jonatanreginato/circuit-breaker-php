<?php

declare(strict_types=1);

$redisClient = new Redis();
$redisClient->connect('host.docker.internal', 6377);
$redisClient->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);

$circuitBreakerMiddleware = new Nuvemshop\NerdTime\CircuitBreaker\CircuitBreakerMiddleware($redisClient, 3, 10);
$handlers = GuzzleHttp\HandlerStack::create();
$handlers->push($circuitBreakerMiddleware, 'circuit_breaker');
$client = new GuzzleHttp\Client(['handler' => $handlers]);

try {
    $response = $client->request('GET', 'https://dummyjson.com/user/2');
    echo "<pre>" . $response->getBody() . "</pre>";
} catch (Throwable $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}
