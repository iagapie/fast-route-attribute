<?php

declare(strict_types=1);

namespace IA\Route\Tests\Dummy\Valid;

use IA\Route\Attribute\Get;
use IA\Route\Attribute\Post;
use IA\Route\Attribute\Prefix;
use IA\Route\Attribute\Route;

#[Prefix('/prefix', DummyMiddleware::class)]
class DummyAction
{
    #[Get('/dummy1-get', 'dummy1-get')]
    #[Route(Route::POST, '/dummy1-post', 'dummy1-post')]
    public function dummy1(): void
    {
    }

    #[Route(Route::DELETE, '/dummy2', 'dummy2', DummyMiddleware::class, DummyMiddleware::class)]
    public function dummy2(): void
    {
    }

    #[Post('/no-private')]
    private function noPrivate(): void
    {}

    #[Post('/no-protected')]
    protected function noProtected(): void
    {}
}