<?php

declare(strict_types=1);

namespace IA\Route\Tests\Attribute;

use IA\Route\Attribute\Finder;
use IA\Route\Attribute\Get;
use IA\Route\Attribute\Prefix;
use IA\Route\Attribute\Route;
use PHPUnit\Framework\TestCase;

class FinderTest extends TestCase
{
    public function testFindAttributes(): void
    {
        $path = __DIR__.'/../Dummy/Valid';

        $data = Finder::create()->find($path);

        $this->assertCount(3, $data);

        foreach ($data as $item) {
            $this->assertCount(2, $item);

            /** @var Route $attribute */
            [$attribute, $handler] = $item;

            $this->assertInstanceOf(Route::class, $attribute);
            $this->assertInstanceOf(Prefix::class, $attribute->prefix);

            $this->assertCount(1, $attribute->prefix->middlewares);

            $this->assertIsString($handler);
            $this->assertStringContainsString('::', $handler);
        }

        $this->assertInstanceOf(Get::class, $data[0][0]);
        $this->assertCount(2, $data[2][0]->middlewares);
    }
}