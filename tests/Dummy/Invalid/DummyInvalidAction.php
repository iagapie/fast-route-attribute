<?php

declare(strict_types=1);

namespace IA\Route\Tests\Dummy\Invalid;

use IA\Route\Attribute\Route;
use IA\Route\Tests\Dummy\Valid\DummyAction;
use IA\Route\Tests\Dummy\Valid\DummyMiddleware;

class DummyInvalidAction
{
    #[Route(Route::POST, '/error', 'error', DummyMiddleware::class, DummyAction::class)]
    public function error(): void
    {
    }
}