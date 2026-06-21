<?php

declare(strict_types=1);

namespace Klax\Http\Runner;

use Klax\Http\Runner\Contract\FallbackRequestHandlerInterface;
use Klax\Http\Runner\Enum\RequestAttribute;
use Klax\Http\Runner\Factory\RequestHandlerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

final readonly class FallbackRequestHandler implements FallbackRequestHandlerInterface
{
    public function __construct(
        private RequestHandlerFactory $factory,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $action = $request->getAttribute(RequestAttribute::ACTION);
        if (!$action) {
            throw new RuntimeException('No _action attribute');
        }

        return $this->factory->create($action)->handle($request);
    }
}
