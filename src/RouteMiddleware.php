<?php

declare(strict_types=1);

namespace IA\Route;

use IA\Route\Resolver\Result;
use IA\Route\Resolver\RouteResolverInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteMiddleware implements MiddlewareInterface
{
    /**
     * RouteMiddleware constructor.
     * @param RouteResolverInterface $resolver
     */
    public function __construct(protected RouteResolverInterface $resolver)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $result = $this->resolver->dispatch($request->getMethod(), $request->getUri()->getPath());

        $request = $request->withAttribute(Result::class, $result);

        return $handler->handle($request);
    }
}