<?php

declare(strict_types=1);

namespace Klax\Http\Runner\Configuration;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final readonly class MiddlewareResolver
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function resolve(string $id): MiddlewareInterface
    {
        try {
            $middleware = $this->container->get($id);
        } catch (ContainerExceptionInterface $e) {
            throw new InvalidArgumentException(
                sprintf('Middleware class "%s" is not registered', $id),
                previous: $e,
            );
        }

        if (!$middleware instanceof MiddlewareInterface) {
            throw new InvalidArgumentException(sprintf(
                'Middleware class "%s" does not implement MiddlewareInterface',
                $id,
            ));
        }

        return $middleware;
    }
}
