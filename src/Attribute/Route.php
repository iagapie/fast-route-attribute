<?php

declare(strict_types=1);

namespace IA\Route\Attribute;

use Attribute;
use IA\Route\Exception\InvalidAttributeException;
use Psr\Http\Server\MiddlewareInterface;

use function is_subclass_of;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_METHOD)]
class Route implements RouteAttribute
{
    public const GET = 'GET';
    public const PUT = 'PUT';
    public const POST = 'POST';
    public const HEAD = 'HEAD';
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';

    /**
     * @var string[]
     */
    public array $middlewares;

    /**
     * @var Prefix|null
     */
    public ?Prefix $prefix = null;

    /**
     * Route constructor.
     * @param string $method
     * @param string $pattern
     * @param string|null $name
     * @param string ...$middlewares
     */
    public function __construct(
        public string $method,
        public string $pattern = '',
        public ?string $name = null,
        string ...$middlewares
    ) {
        $this->middlewares = $middlewares;

        foreach ($middlewares as $middleware) {
            if (false === is_subclass_of($middleware, MiddlewareInterface::class)) {
                throw InvalidAttributeException::new($middleware);
            }
        }
    }
}