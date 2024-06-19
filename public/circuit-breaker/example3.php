<?php

declare(strict_types=1);

$redisClient = new Redis();
$redisClient->connect('host.docker.internal', 6377);
$redisClient->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_IGBINARY);

$ganesha = Ackintosh\Ganesha\Builder::withRateStrategy()
    ->minimumRequests(3)
    ->timeWindow(30)
    ->failureRateThreshold(5)
    ->intervalToHalfOpen(60)
    ->adapter(new Ackintosh\Ganesha\Storage\Adapter\Redis($redisClient))
    ->build();

$guzzleMiddleware = new Ackintosh\Ganesha\GuzzleMiddleware($ganesha);
$handlers = GuzzleHttp\HandlerStack::create();
$handlers->push($guzzleMiddleware, 'circuit_breaker');
$client = new GuzzleHttp\Client(['handler' => $handlers]);

try {
    $response = $client->get('https://dummyjson.com/user/3');
    echo "<pre>" . $response->getBody() . "</pre>";
} catch (Ackintosh\Ganesha\Exception\RejectedException | GuzzleHttp\Exception\GuzzleException $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}
