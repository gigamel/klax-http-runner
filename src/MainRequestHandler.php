<?php

declare(strict_types=1);

namespace Klax\Http\Runner;

use Klax\Http\Runner\Contract\FallbackRequestHandlerInterface;
use Klax\Http\Runner\Contract\MainRequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class MainRequestHandler implements MainRequestHandlerInterface
{
    private RequestHandlerInterface $pipelineCompiled;

    public function __construct(
        FallbackRequestHandlerInterface $fallbackRequestHandler,
        /** @var list<MiddlewareInterface> $middlewares */
        array $middlewares = [],
    ) {
        $this->pipelineCompiled = $this->compilePipeline($fallbackRequestHandler, $middlewares, 0);
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->pipelineCompiled->handle($request);
    }

    /**
     * @param list<MiddlewareInterface> $middlewares
     */
    private function compilePipeline(
        RequestHandlerInterface $fallbackRequestHandler,
        array $middlewares,
        int $index,
    ): RequestHandlerInterface {
        $middleware = $middlewares[$index] ?? null;
        if (null === $middleware) {
            return $fallbackRequestHandler;
        }

        return new readonly class(
            $middleware,
            $this->compilePipeline($fallbackRequestHandler, $middlewares, $index + 1),
        ) implements RequestHandlerInterface {
            public function __construct(
                private MiddlewareInterface $middleware,
                private RequestHandlerInterface $requestHandler,
            ) {
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->requestHandler);
            }
        };
    }
}
