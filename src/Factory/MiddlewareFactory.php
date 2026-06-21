<?php

declare(strict_types=1);

namespace Klax\Http\Runner\Factory;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final readonly class MiddlewareFactory
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(string $id): MiddlewareInterface
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
