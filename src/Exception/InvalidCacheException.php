<?php

declare(strict_types=1);

namespace IA\Route\Exception;

use RuntimeException;

use function sprintf;

class InvalidCacheException extends RuntimeException implements RouteException
{
    /**
     * @param string $cacheFile
     * @return static
     */
    public static function new(string $cacheFile): self
    {
        return new self(sprintf('Invalid cache file "%s"', $cacheFile));
    }
}