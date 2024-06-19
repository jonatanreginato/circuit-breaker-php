<?php

declare(strict_types=1);

namespace Nuvemshop\NerdTime\CircuitBreaker;

use Closure;
use Exception;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\RejectedPromise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class CircuitBreakerMiddleware
{
    public function __construct(
        private \Redis $redisClient,
        private readonly mixed $maxFailures = 3,
        private readonly int $resetTimeout = 10
    ) {
    }

    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $failures = $this->redisClient->get('failures');
            $lastAttempt = $this->redisClient->get('lastAttempt');

            if ($failures >= $this->maxFailures && (time() - $lastAttempt) < $this->resetTimeout) {
                return new RejectedPromise(new Exception('Circuit Breaker is open'));
            }

            $this->redisClient->set('lastAttempt', time());
            return $handler($request, $options)->then(
                function (ResponseInterface $response) {
                    $this->redisClient->set('failures', 0);
                    return $response;
                },
                function (Throwable $e) {
                    $this->redisClient->set('failures', (int)$this->redisClient->get('failures') + 1);
                    return Create::rejectionFor($e);
                }
            );
        };
    }
}
