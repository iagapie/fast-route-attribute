<?php

declare(strict_types=1);

namespace IA\Route\Tests\Attribute;

use IA\Route\Attribute\Route;
use IA\Route\Exception\InvalidAttributeException;
use IA\Route\Tests\Dummy\Invalid\DummyInvalidAction;
use IA\Route\Tests\Dummy\Valid\DummyAction;
use PHPUnit\Framework\TestCase;
use ReflectionAttribute;
use ReflectionException;
use ReflectionMethod;

class RouteTest extends TestCase
{
    public function testMultipleAttributes(): void
    {
        $method = 'dummy1';

        $attributes = $this->getAttributes(DummyAction::class, $method);

        $this->assertCount(2, $attributes);
        $this->assertEquals(Route::GET, $attributes[0]->newInstance()->method);
        $this->assertEquals(Route::POST, $attributes[1]->newInstance()->method);
    }

    public function testMultipleMiddlewares(): void
    {
        $method = 'dummy2';

        $attributes = $this->getAttributes(DummyAction::class, $method);

        $this->assertCount(1, $attributes);

        /** @var Route $route */
        $route = $attributes[0]->newInstance();

        $this->assertEquals(Route::DELETE, $route->method);
        $this->assertEquals('/'.$method, $route->pattern);
        $this->assertEquals($method, $route->name);
        $this->assertCount(2, $route->middlewares);
    }

    public function testMiddlewareError(): void
    {
        $method = 'error';

        $attributes = $this->getAttributes(DummyInvalidAction::class, $method);

        $this->assertCount(1, $attributes);

        $this->expectException(InvalidAttributeException::class);

        $attributes[0]->newInstance();
    }

    /**
     * @param string $class
     * @param string $method
     * @return ReflectionAttribute[]
     * @throws ReflectionException
     */
    private function getAttributes(string $class, string $method): array
    {
        $refMethod = new ReflectionMethod($class, $method);

        return $refMethod->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);
    }
}