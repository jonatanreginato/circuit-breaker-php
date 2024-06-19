<?php

declare(strict_types=1);

$ganesha = Ackintosh\Ganesha\Builder::withCountStrategy()
    ->failureCountThreshold(2)
    ->intervalToHalfOpen(10)
    ->adapter(new Ackintosh\Ganesha\Storage\Adapter\Apcu())
    ->build();

$guzzleMiddleware = new Ackintosh\Ganesha\GuzzleMiddleware($ganesha);
$handlers = GuzzleHttp\HandlerStack::create();
$handlers->push($guzzleMiddleware, 'circuit_breaker');
$client = new GuzzleHttp\Client(['handler' => $handlers]);

try {
    $response = $client->get('https://dummyjsondsfdsd.com/user/4');
    echo "<pre>" . $response->getBody() . "</pre>";
} catch (Ackintosh\Ganesha\Exception\RejectedException | GuzzleHttp\Exception\GuzzleException $e) {
    echo get_class($e) . ': ' . $e->getMessage();
}
