<?php

declare(strict_types=1);

namespace Klax\Http\Runner\Middleware;

use Klax\Http\Runner\Configuration\ThrowableHandlerResolver;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final readonly class ThrowableMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ThrowableHandlerResolver $throwableHandlerResolver,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (Throwable $throwable) {
            return $this->throwableHandlerResolver->resolve($throwable)->handle($request, $throwable);
        }
    }
}
