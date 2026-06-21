<?php

declare(strict_types=1);

namespace Klax\Http\Runner\Factory;

use InvalidArgumentException;
use Klax\Http\Runner\Contract\ThrowableHandlerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Throwable;

final readonly class ThrowableHandlerFactory
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(Throwable $throwable): ThrowableHandlerInterface
    {
        $interfaceName = $this->getInterfaceNameWhichHasContainer($throwable);
        if (null === $interfaceName) {
            throw new InvalidArgumentException(sprintf(
                'Throwable handler for class "%s" is not registered in the container',
                $throwable::class,
            ));
        }

        try {
            $throwableHandler = $this->container->get($interfaceName);
        } catch (ContainerExceptionInterface $e) {
            throw new InvalidArgumentException(
                sprintf(
                    'Throwable handler "%s" does not exist',
                    $interfaceName,
                ),
                previous: $e,
            );
        }

        if (!$throwableHandler instanceof ThrowableHandlerInterface) {
            throw new InvalidArgumentException(sprintf(
                'Throwable handler "%s" does not implement ThrowableHandlerInterface',
                $throwableHandler::class,
            ));
        }

        return $throwableHandler;
    }

    private function getInterfaceNameWhichHasContainer(Throwable $throwable): ?string
    {
        $throwableClass = $throwable::class;
        if ($this->container->has($throwableClass)) {
            return $throwableClass;
        }

        foreach (class_implements($throwableClass) ?: [] as $interfaceName) {
            if (Throwable::class === $interfaceName) {
                continue;
            }

            if ($this->container->has($interfaceName)) {
                return $interfaceName;
            }
        }

        foreach (class_parents($throwableClass) ?: [] as $parentClass) {
            if ($this->container->has($parentClass)) {
                return $parentClass;
            }
        }

        if ($this->container->has(Throwable::class)) {
            return Throwable::class;
        }

        return null;
    }
}
