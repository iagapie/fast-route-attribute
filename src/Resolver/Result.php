<?php

declare(strict_types=1);

namespace IA\Route\Resolver;

use function rawurldecode;

class Result
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    public function __construct(
        protected string $method,
        protected string $uri,
        protected int $status,
        protected ?string $identifier = null,
        protected array $arguments = [],
        protected array $allowedMethods = []
    ) {
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param bool $urlDecode
     * @return array
     */
    public function getArguments(bool $urlDecode = true): array
    {
        if (!$urlDecode) {
            return $this->arguments;
        }

        $arguments = [];

        foreach ($this->arguments as $key => $value) {
            $arguments[$key] = rawurldecode($value);
        }

        return $arguments;
    }

    /**
     * @return array
     */
    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}