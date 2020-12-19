<?php

declare(strict_types=1);

namespace IA\Route\Tests\Collector;

use FastRoute\DataGenerator\GroupCountBased;
use FastRoute\RouteParser\Std;
use IA\Route\Collector\RouteCollector;
use IA\Route\RouteInterface;
use IA\Route\Tests\Dummy\Valid\DummyMiddleware;
use PHPUnit\Framework\TestCase;

class RouteCollectorTest extends TestCase
{
    public function testAddRoute(): void
    {
        $collector = new RouteCollector(new Std(), new GroupCountBased());

        $data = [
            [['POST', '/login', 'Auth::login'], 'login', []],
            [['GET', '/logout', 'Auth::logout'], 'logout', [DummyMiddleware::class]],
        ];

        foreach ($data as $item) {
            $collector->add(...$item[0])->setName($item[1])->setMiddlewares($item[2]);
        }

        $this->assertCount(2, $collector->routes());
        $this->assertContainsOnlyInstancesOf(RouteInterface::class, $collector->routes());

        foreach ($data as $i => $item) {
            $route = $collector->routes()['route'.$i];

            $this->assertNotEmpty($route->getExpressions());
            $this->assertEquals($item[0][0], $route->getMethod());
            $this->assertEquals($item[0][1], $route->getPattern());
            $this->assertEquals($item[0][2], $route->getHandler());
            $this->assertEquals($item[1], $route->getName());
            $this->assertEquals($item[2], $route->getMiddlewares());
        }
    }
}