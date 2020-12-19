<?php

declare(strict_types=1);

namespace IA\Route\Generator;

use IA\Route\Exception\InvalidUrlDataException;
use IA\Route\Resolver\RouteResolverInterface;
use Psr\Http\Message\UriInterface;

use function array_key_exists;
use function http_build_query;
use function implode;
use function is_string;

class UrlGenerator implements UrlGeneratorInterface
{
    /**
     * UrlGenerator constructor.
     * @param RouteResolverInterface $resolver
     */
    public function __construct(protected RouteResolverInterface $resolver)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function generate(string $routeName, array $data = [], array $queryParams = []): string
    {
        $route = $this->resolver->route($routeName);

        $segments = [];
        $segmentName = '';

        /*
         * $routes is an associative array of expressions representing a route as multiple segments
         * There is an expression for each optional parameter plus one without the optional parameters
         * The most specific is last, hence why we reverse the array before iterating over it
         */
        foreach ($route->getExpressions() as $expression) {
            foreach ($expression as $segment) {
                /*
                 * Each $segment is either a string or an array of strings
                 * containing optional parameters of an expression
                 */
                if (is_string($segment)) {
                    $segments[] = $segment;
                    continue;
                }

                /*
                 * If we don't have a data element for this segment in the provided $data
                 * we cancel testing to move onto the next expression with a less specific item
                 */
                if (!array_key_exists($segment[0], $data)) {
                    $segments = [];
                    $segmentName = $segment[0];
                    break;
                }

                $segments[] = $data[$segment[0]];
            }

            /*
             * If we get to this logic block we have found all the parameters
             * for the provided $data which means we don't need to continue testing
             * less specific expressions
             */
            if (!empty($segments)) {
                break;
            }
        }

        if (empty($segments)) {
            throw InvalidUrlDataException::new('Missing data for URL segment: '.$segmentName);
        }

        $url = implode('', $segments);

        if ($queryParams) {
            $url .= '?'.http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * {@inheritDoc}
     */
    public function absolute(UriInterface $uri, string $routeName, array $data = [], array $queryParams = []): string
    {
        $path = $this->generate($routeName, $data, $queryParams);
        $scheme = $uri->getScheme();
        $authority = $uri->getAuthority();
        $protocol = ($scheme ? $scheme.':' : '').($authority ? '//'.$authority : '');

        return $protocol.$path;
    }
}