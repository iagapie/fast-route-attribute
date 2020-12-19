<?php

declare(strict_types=1);

namespace IA\Route\Exception;

use RuntimeException;

final class RouteNotFoundException extends RuntimeException implements RouteException
{
    /**
     * @param string $message
     * @return static
     */
    public static function new(string $message): self
    {
        return new self($message);
    }
}