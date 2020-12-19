<?php

declare(strict_types=1);

namespace IA\Route\Generator;

use Psr\Http\Message\UriInterface;

interface UrlGeneratorInterface
{
    /**
     * @param string $routeName
     * @param array $data
     * @param array $queryParams
     * @return string
     */
    public function generate(string $routeName, array $data = [], array $queryParams = []): string;

    /**
     * @param UriInterface $uri
     * @param string $routeName
     * @param array $data
     * @param array $queryParams
     * @return string
     */
    public function absolute(UriInterface $uri, string $routeName, array $data = [], array $queryParams = []): string;
}