<?php

declare(strict_types=1);

namespace IA\Route;

interface RouteInterface
{
    /**
     * @return string
     */
    public function getIdentifier(): string;

    /**
     * @return string
     */
    public function getMethod(): string;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @param string|null $name
     * @return RouteInterface
     */
    public function setName(?string $name): RouteInterface;

    /**
     * @return string
     */
    public function getPattern(): string;

    /**
     * @return array
     */
    public function getExpressions(): array;

    /**
     * @return string
     */
    public function getHandler(): string;

    /**
     * @return string[]
     */
    public function getMiddlewares(): array;

    /**
     * @param array $middlewares
     * @return RouteInterface
     */
    public function setMiddlewares(array $middlewares): RouteInterface;

    /**
     * @param string $middleware
     * @return RouteInterface
     */
    public function addMiddleware(string $middleware): RouteInterface;

    /**
     * Returns all the necessary state of the object for serialization purposes.
     */
    public function __serialize(): array;

    /**
     * Restores the object state from an array given by __serialize().
     * @param array $data
     */
    public function __unserialize(array $data): void;
}