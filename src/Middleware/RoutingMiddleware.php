<?php

declare(strict_types=1);

namespace Klax\Http\Runner\Middleware;

use Klax\Http\Router\Contract\Exception\RouteNotFoundException;
use Klax\Http\Router\Contract\RouterInterface;
use Klax\Http\Runner\Enum\RequestAttribute;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class RoutingMiddleware implements MiddlewareInterface
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    /**
     * @throws RouteNotFoundException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $routeShard = $this->router->handleRequest($request);
        foreach ($routeShard->getSegments() as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        return $handler->handle($request->withAttribute(RequestAttribute::ACTION, $routeShard->getHandler()));
    }
}
