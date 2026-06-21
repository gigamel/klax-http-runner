<?php

declare(strict_types=1);

namespace Klax\Http\Runner\Middleware;

use Klax\Http\Runner\Factory\ThrowableHandlerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

final readonly class ThrowableMiddleware implements MiddlewareInterface
{
    public function __construct(
        private ThrowableHandlerFactory $factory,
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
            return $this->factory->create($throwable)->handle($request, $throwable);
        }
    }
}
