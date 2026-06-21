<?php

declare(strict_types=1);

namespace Klax\Http\Runner;

use Klax\Http\Protocol\Exception\NotFoundException;
use Klax\Http\Runner\Contract\FallbackRequestHandlerInterface;
use Klax\Http\Runner\Enum\RequestAttribute;
use Klax\Http\Runner\Configuration\RequestHandlerResolver;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class FallbackRequestHandler implements FallbackRequestHandlerInterface
{
    public function __construct(
        private RequestHandlerResolver $requestHandlerResolver,
    ) {
    }

    /**
     * @throws NotFoundException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $action = $request->getAttribute(RequestAttribute::ACTION);
        if (!$action) {
            throw new NotFoundException('No _action attribute');
        }

        return $this->requestHandlerResolver->resolve($action)->handle($request);
    }
}
