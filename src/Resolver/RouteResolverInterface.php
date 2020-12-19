<?php

declare(strict_types=1);

namespace IA\Route\Resolver;

use IA\Route\Exception\RouteNotFoundException;
use IA\Route\RouteInterface;

interface RouteResolverInterface
{
    /**
     * @param string $method
     * @param string $uri should be $request->getUri()->getPath()
     * @return Result
     */
    public function dispatch(string $method, string $uri): Result;

    /**
     * @param string $identifier
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function resolve(string $identifier): RouteInterface;

    /**
     * @param string $name
     * @return RouteInterface
     * @throws RouteNotFoundException
     */
    public function route(string $name): RouteInterface;

    /**
     * @return array<string, RouteInterface>
     */
    public function routes(): array;
}