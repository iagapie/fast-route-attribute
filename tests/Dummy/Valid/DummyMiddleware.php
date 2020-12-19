<?php

declare(strict_types=1);

namespace IA\Route\Tests\Dummy\Valid;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DummyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new class implements ResponseInterface {
            public function getProtocolVersion()
            {
            }

            public function withProtocolVersion($version)
            {
            }

            public function getHeaders()
            {
            }

            public function hasHeader($name)
            {
            }

            public function getHeader($name)
            {
            }

            public function getHeaderLine($name)
            {
            }

            public function withHeader($name, $value)
            {
            }

            public function withAddedHeader($name, $value)
            {
            }

            public function withoutHeader($name)
            {
            }

            public function getBody()
            {
            }

            public function withBody(StreamInterface $body)
            {
            }

            public function getStatusCode()
            {
            }

            public function withStatus($code, $reasonPhrase = '')
            {
            }

            public function getReasonPhrase()
            {
            }
        };
    }
}