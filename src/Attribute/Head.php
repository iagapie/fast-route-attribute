<?php

declare(strict_types=1);

namespace IA\Route\Attribute;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
final class Head extends Route
{
    /**
     * Head constructor.
     * @param string $pattern
     * @param string|null $name
     * @param string ...$middlewares
     */
    public function __construct(string $pattern = '', ?string $name = null, string ...$middlewares)
    {
        parent::__construct(self::HEAD, $pattern, $name, ...$middlewares);
    }
}