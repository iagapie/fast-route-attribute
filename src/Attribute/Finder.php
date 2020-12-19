<?php

declare(strict_types=1);

namespace IA\Route\Attribute;

use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\Finder\Finder as SfFinder;
use Symfony\Component\Finder\SplFileInfo;

use function array_map;
use function class_exists;
use function fclose;
use function feof;
use function fopen;
use function fread;
use function preg_match;
use function sprintf;
use function trim;

class Finder
{
    /**
     * @return static
     */
    public static function create(): static
    {
        return new static();
    }

    /**
     * @param string ...$dirs
     * @return array
     */
    public function find(string ...$dirs): array
    {
        $files = SfFinder::create()->files()->name('*.php')->in($dirs);

        $data = [];

        foreach ($files as $file) {
            $className = $this->findClassName($file);

            if (!class_exists($className)) {
                continue;
            }

            $data = [...$data, ...$this->findClassAttributes($className)];
        }

        return $data;
    }

    /**
     * @param string $className
     * @return array
     */
    public function findClassAttributes(string $className): array
    {
        $class = new ReflectionClass($className);

        if ($class->isInterface() || $class->isAbstract()) {
            return [];
        }

        $prefixes = $this->findPrefixes($class);

        $data = [];

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isStatic()) {
                continue;
            }

            $attributes = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);

            foreach ($attributes as $attribute) {
                /** @var Route $route */
                $instance = $attribute->newInstance();

                $handler = sprintf('%s::%s', $className, $method->getName());

                if (empty($prefixes)) {
                    $data[] = [$instance, $handler];

                    continue;
                }

                foreach ($prefixes as $prefix) {
                    $route = clone $instance;
                    $route->prefix = $prefix;
                    $data[] = [$route, $handler];
                }
            }
        }

        return $data;
    }

    /**
     * @param ReflectionClass $class
     * @return array<Prefix>
     */
    protected function findPrefixes(ReflectionClass $class): array
    {
        return array_map(
            fn(ReflectionAttribute $attribute) => $attribute->newInstance(),
            $class->getAttributes(Prefix::class)
        );
    }

    /**
     * @param SplFileInfo $file
     * @return string
     */
    protected function findClassName(SplFileInfo $file): string
    {
        $fp = fopen($file->getRealPath(), 'r');

        if (!$fp) {
            return $file->getFilenameWithoutExtension();
        }

        $buffer = $namespace = '';

        while (true) {
            if (feof($fp)) {
                break;
            }

            if ($b = fread($fp, 512)) {
                $buffer .= $b;
            } else {
                break;
            }

            if (preg_match('#^namespace\s+(.+?);$#sm', $buffer, $matches)) {
                $namespace = $matches[1];
                break;
            }
        }

        fclose($fp);

        return trim($namespace.'\\'.$file->getFilenameWithoutExtension());
    }
}