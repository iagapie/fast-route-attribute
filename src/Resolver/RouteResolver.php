<?php

declare(strict_types=1);

namespace IA\Route\Resolver;

use FastRoute\Dispatcher;
use IA\Route\Attribute\Finder;
use IA\Route\Attribute\Route;
use IA\Route\Exception\InvalidCacheException;
use IA\Route\Exception\RouteNotFoundException;
use IA\Route\Collector\RouteCollectorInterface;
use IA\Route\RouteInterface;

use function file_exists;
use function file_put_contents;
use function is_array;
use function rawurldecode;
use function var_export;

class RouteResolver implements RouteResolverInterface
{
    protected const CACHE_FILE = '/__route.cache';

    /**
     * @var array<string, RouteInterface>
     */
    protected array $routes = [];

    /**
     * @var array
     */
    protected array $data = [];

    /**
     * @var Dispatcher|null
     */
    private ?Dispatcher $dispatcher = null;

    /**
     * RouteResolver constructor.
     * @param RouteCollectorInterface $routeCollector
     * @param bool $debug
     * @param string $cacheDir
     * @param array $handlerDirs
     * @param string $dispatcherClass
     */
    public function __construct(
        protected RouteCollectorInterface $routeCollector,
        protected bool $debug,
        protected string $cacheDir,
        protected array $handlerDirs,
        protected string $dispatcherClass = Dispatcher\GroupCountBased::class
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(string $method, string $uri): Result
    {
        $uri = rawurldecode($uri);

        if ('' === $uri || '/' !== $uri[0]) {
            $uri = '/'.$uri;
        }

        $data = $this->dispatcher()->dispatch($method, $uri);

        return match ($data[0]) {
            Dispatcher::METHOD_NOT_ALLOWED => new Result($method, $uri, $data[0], allowedMethods: $data[1]),
            Dispatcher::FOUND, Dispatcher::NOT_FOUND => new Result($method, $uri, ...$data)
        };
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(string $identifier): RouteInterface
    {
        return $this->routes()[$identifier]
            ?? throw RouteNotFoundException::new('Route not found, looks like your route cache is stale.');
    }

    /**
     * {@inheritDoc}
     */
    public function route(string $name): RouteInterface
    {
        foreach ($this->routes() as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        throw RouteNotFoundException::new(sprintf('Named route does not exist for name: %s', $name));
    }

    /**
     * {@inheritDoc}
     */
    public function routes(): array
    {
        $this->loadData();

        return $this->routes;
    }

    /**
     * @return Dispatcher
     */
    protected function dispatcher(): Dispatcher
    {
        if (null !== $this->dispatcher) {
            return $this->dispatcher;
        }

        $this->loadData();

        $class = $this->dispatcherClass;

        return $this->dispatcher = new $class($this->data);
    }

    protected function loadData(): void
    {
        if (!empty($this->routes) && !empty($this->data)) {
            return;
        }

        if ($this->useCache()) {
            [$this->routes, $this->data] = $this->loadCache(static::CACHE_FILE);

            if (!empty($this->routes) && !empty($this->data)) {
                return;
            }
        }

        /** @var Route $attribute */
        foreach (Finder::create()->find(...$this->handlerDirs) as [$attribute, $handler]) {
            $prefix = $attribute->prefix?->prefix ?? '';
            $middlewares = $attribute->prefix?->middlewares ?? [];

            $this->routeCollector
                ->add($attribute->method, $prefix.$attribute->pattern, $handler)
                ->setName($attribute->name)
                ->setMiddlewares([...$middlewares, ...$attribute->middlewares]);
        }

        $this->routes = $this->routeCollector->routes();
        $this->data = $this->routeCollector->data();

        if ($this->useCache()) {
            $this->saveCache(static::CACHE_FILE, [$this->routes, $this->data]);
        }
    }

    /**
     * @return bool
     */
    protected function useCache(): bool
    {
        return !$this->debug && !empty($this->cacheDir);
    }

    /**
     * @param string $fileName
     * @return array
     * @throws InvalidCacheException
     */
    protected function loadCache(string $fileName): array
    {
        $cacheFile = $this->cacheDir.$fileName;

        if (!file_exists($cacheFile)) {
            return [];
        }

        $data = require $cacheFile;

        return is_array($data) && 2 === count($data) ? $data : throw InvalidCacheException::new($cacheFile);
    }

    /**
     * @param string $fileName
     * @param array $data
     */
    protected function saveCache(string $fileName, array $data): void
    {
        $cacheFile = $this->cacheDir.$fileName;

        file_put_contents($cacheFile, '<?php return '.var_export($data, true).';');
    }
}