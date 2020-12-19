<?php

declare(strict_types=1);

namespace IA\Route\Exception;

use InvalidArgumentException;

final class InvalidUrlDataException extends InvalidArgumentException implements RouteException
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