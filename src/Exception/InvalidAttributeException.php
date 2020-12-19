<?php

declare(strict_types=1);

namespace IA\Route\Exception;

use InvalidArgumentException;
use Psr\Http\Server\MiddlewareInterface;

use function sprintf;

class InvalidAttributeException extends InvalidArgumentException implements RouteException
{
    /**
     * @param string $class
     * @return static
     */
    public static function new(string $class): self
    {
        return new self(
            sprintf('Middleware "%s" MUST implement %s', $class, MiddlewareInterface::class)
        );
    }
}