<?php

declare(strict_types=1);

namespace IA\Route;

class Route implements RouteInterface
{
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var string[]
     */
    protected array $middlewares = [];

    /**
     * Route constructor.
     * @param string $identifier
     * @param string $method
     * @param string $pattern
     * @param string $handler
     * @param array $expressions
     */
    public function __construct(
        protected string $identifier,
        protected string $method,
        protected string $pattern,
        protected string $handler,
        protected array $expressions = []
    ) {
        $this->name = $identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritDoc}
     */
    public function setName(?string $name): RouteInterface
    {
        $this->name = $name ?: $this->identifier;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * {@inheritDoc}
     */
    public function getExpressions(): array
    {
        return $this->expressions;
    }

    /**
     * {@inheritDoc}
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * {@inheritDoc}
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * {@inheritDoc}
     */
    public function setMiddlewares(array $middlewares): RouteInterface
    {
        $this->middlewares = $middlewares;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function addMiddleware(string $middleware): RouteInterface
    {
        $this->middlewares[] = $middleware;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function __serialize(): array
    {
        return [
            $this->name,
            $this->identifier,
            $this->method,
            $this->pattern,
            $this->handler,
            $this->expressions,
            $this->middlewares,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function __unserialize(array $data): void
    {
        [
            $this->name,
            $this->identifier,
            $this->method,
            $this->pattern,
            $this->handler,
            $this->expressions,
            $this->middlewares,
        ] = $data;
    }
}