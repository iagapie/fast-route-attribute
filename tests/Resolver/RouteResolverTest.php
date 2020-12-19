<?php

declare(strict_types=1);

namespace IA\Route\Tests\Resolver;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteParser\Std;
use IA\Route\Collector\RouteCollector;
use IA\Route\Exception\RouteNotFoundException;
use IA\Route\Resolver\Result;
use IA\Route\Resolver\RouteResolver;
use IA\Route\Resolver\RouteResolverInterface;
use IA\Route\RouteInterface;
use IA\Route\Tests\Dummy\Valid\DummyAction;
use PHPUnit\Framework\TestCase;

class RouteResolverTest extends TestCase
{
    /**
     * @var RouteResolverInterface|null
     */
    protected ?RouteResolverInterface $resolver;

    protected function setUp(): void
    {
        $routeCollector = new RouteCollector(new Std(), new GroupCountBased());
        $paths = [__DIR__.'/../Dummy/Valid'];
        $this->resolver = new RouteResolver($routeCollector, true, '', $paths);
    }

    protected function tearDown(): void
    {
        $this->resolver = null;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $identifier
     * @dataProvider provideTestDispatch
     */
    public function testDispatchFound(string $method, string $uri, string $identifier): void
    {
        $result = $this->resolver->dispatch($method, $uri);

        $this->assertEquals(Result::FOUND, $result->getStatus());
        $this->assertEquals($method, $result->getMethod());
        $this->assertEquals($uri, $result->getUri());
        $this->assertEquals($identifier, $result->getIdentifier());
    }

    public function testDispatchNodFound(): void
    {
        $method = 'GET';
        $uri = '/not-found';
        $result = $this->resolver->dispatch($method, $uri);

        $this->assertEquals(Result::NOT_FOUND, $result->getStatus());
        $this->assertEquals($method, $result->getMethod());
        $this->assertEquals($uri, $result->getUri());
    }

    public function testDispatchMethodNodAllowed(): void
    {
        $method = 'GET';
        $uri = '/prefix/dummy2';
        $result = $this->resolver->dispatch($method, $uri);

        $this->assertEquals(Result::METHOD_NOT_ALLOWED, $result->getStatus());
        $this->assertEquals($method, $result->getMethod());
        $this->assertEquals($uri, $result->getUri());
        $this->assertContains('DELETE', $result->getAllowedMethods());
    }

    /**
     * @param string $identifier
     * @param string $name
     * @param string $handler
     * @dataProvider provideTestRoute
     */
    public function testResolveRoute(string $identifier, string $name, string $handler): void
    {
        $route = $this->resolver->resolve($identifier);

        $this->assertInstanceOf(RouteInterface::class, $route);
        $this->assertEquals($name, $route->getName());
        $this->assertEquals($handler, $route->getHandler());
    }

    /**
     * @param string $identifier
     * @param string $name
     * @param string $handler
     * @dataProvider provideTestRoute
     */
    public function testFindRoute(string $identifier, string $name, string $handler): void
    {
        $route = $this->resolver->route($name);

        $this->assertInstanceOf(RouteInterface::class, $route);
        $this->assertEquals($identifier, $route->getIdentifier());
        $this->assertEquals($handler, $route->getHandler());
    }

    public function testResolveError(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->resolver->resolve('no-test-route');
    }

    public function testFindRouteError(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->resolver->route('no-test-route');
    }

    /**
     * @return iterable
     */
    public function provideTestDispatch(): iterable
    {
        yield ['GET', '/prefix/dummy1-get', 'route0'];
        yield ['POST', '/prefix/dummy1-post', 'route1'];
        yield ['DELETE', '/prefix/dummy2', 'route2'];
    }

    /**
     * @return iterable
     */
    public function provideTestRoute(): iterable
    {
        yield ['route0', 'dummy1-get', DummyAction::class.'::dummy1'];
        yield ['route1', 'dummy1-post', DummyAction::class.'::dummy1'];
        yield ['route2', 'dummy2', DummyAction::class.'::dummy2'];
    }
}