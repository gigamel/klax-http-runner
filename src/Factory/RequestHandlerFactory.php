<?php

declare(strict_types=1);

namespace Klax\Http\Runner\Factory;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class RequestHandlerFactory
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(string $id): RequestHandlerInterface
    {
        try {
            $requestHandler = $this->container->get($id);
        } catch (ContainerExceptionInterface) {
            throw new InvalidArgumentException(sprintf(
                'Request handler "%s" does not exist',
                $id,
            ));
        }

        if (!$requestHandler instanceof RequestHandlerInterface) {
            throw new InvalidArgumentException(sprintf(
                'Request handler "%s" does not implement RequestHandlerInterface',
                $id,
            ));
        }

        return $requestHandler;
    }
}
