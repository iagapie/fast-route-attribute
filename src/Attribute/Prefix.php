<?php

declare(strict_types=1);

namespace IA\Route\Attribute;

use Attribute;
use IA\Route\Exception\InvalidAttributeException;
use Psr\Http\Server\MiddlewareInterface;

use function is_subclass_of;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
final class Prefix implements RouteAttribute
{
    /**
     * @var string[]
     */
    public array $middlewares;

    /**
     * Prefix constructor.
     * @param string $prefix
     * @param string ...$middlewares
     */
    public function __construct(public string $prefix, string ...$middlewares)
    {
        $this->middlewares = $middlewares;

        foreach ($middlewares as $middleware) {
            if (false === is_subclass_of($middleware, MiddlewareInterface::class)) {
                throw InvalidAttributeException::new($middleware);
            }
        }
    }
}