<?php

declare(strict_types=1);

namespace IA\Route\Collector;

use IA\Route\RouteInterface;

interface RouteCollectorInterface
{
    /**
     * @param string $method
     * @param string $pattern
     * @param mixed $handler
     * @return RouteInterface
     */
    public function add(string $method, string $pattern, mixed $handler): RouteInterface;

    /**
     * @param string $prefix
     * @param callable $callback
     * @return RouteCollectorInterface
     */
    public function group(string $prefix, callable $callback): RouteCollectorInterface;

    /**
     * @return array<string, RouteInterface>
     */
    public function routes(): array;

    /**
     * Returns the collected route data, as provided by the data generator.
     *
     * @return array
     */
    public function data(): array;
}