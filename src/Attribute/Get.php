<?php

declare(strict_types=1);

namespace IA\Route\Attribute;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
final class Get extends Route
{
    /**
     * Get constructor.
     * @param string $pattern
     * @param string|null $name
     * @param string ...$middlewares
     */
    public function __construct(string $pattern, ?string $name = null, string ...$middlewares)
    {
        parent::__construct(self::GET, $pattern, $name, ...$middlewares);
    }
}