<?php

namespace Kami\ApiCoreBundle\Routing;

use Kami\ApiCoreBundle\Controller\ApiController;
use Kami\ApiCoreBundle\Model\UserAwareInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ApiCoreRoutingLoader extends Loader
{
    private bool $loaded = false;

    private array $resources;

    private string $defaultLocale;

    private array $locales;

    public function __construct(array $resources, array $locales, string $defaultLocale)
    {
        $this->resources = $resources;
        $this->defaultLocale = $defaultLocale;
        $this->locales = $locales;
    }

    /**
     * @param mixed $resource
     * @param string $type
     * @return RouteCollection
     */
    public function load($resource, ?string $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "api" loader twice');
        }

        $routes = new RouteCollection();

        foreach ($this->resources as $restResource) {
            $routes->add(
                sprintf('kami.api_core_%s_index', $restResource['name']),
                $this->createIndexRoute($restResource)
            );
            $routes->add(
                sprintf('kami.api_core_%s_filter', $restResource['name']),
                $this->createFilterRoute($restResource)
            );
            $routes->add(
                sprintf('kami.api_core_%s_item', $restResource['name']),
                $this->createItemRoute($restResource)
            );
            $routes->add(
                sprintf('kami.api_core_%s_create', $restResource['name']),
                $this->createNewRoute($restResource)
            );
            $routes->add(
                sprintf('kami.api_core_%s_update', $restResource['name']),
                $this->createUpdateRoute($restResource)
            );
            $routes->add(
                sprintf('kami.api_core_%s_delete', $restResource['name']),
                $this->createDeleteRoute($restResource)
            );
            if ($restResource['entity'] instanceof UserAwareInterface) {
                $routes->add(
                    sprintf('kami.api_core_%s_my', $restResource['name']),
                    $this->createMyRoute($restResource)
                );
            }
        }

        $this->loaded = true;

        return $routes;
    }

    public function supports($resource, $type = null) : bool
    {
        return 'kami_api_core' === $type;
    }


    private function createIndexRoute(array $resource)
    {
        $path = sprintf('/api/{_locale}{_S}%s{_dot}{_format}', $resource['name']);
        $defaults = [
            '_controller' => ApiController::class.'::apiAction',
            '_locale' => $this->defaultLocale,
            '_entity' => $resource['entity'],
            '_strategy' => $resource['strategies']['index'],
            '_request_processor' => $resource['request_processor'],
            '_sort' => $resource['default_sort'],
            '_resource_name' => $resource['name'],
            '_sort_direction' => $resource['default_sort_direction'],
            '_format' => 'json'
        ];
        $requirements = [
            '_S' => '/?',
            '_dot' => '\.?',
            '_locale' => '|'.implode('|', $this->locales),
            '_format' => 'json|xml'
        ];
        $route = new Route($path, $defaults, $requirements, [], '', [], ['GET']);

        return $route;
    }

    private function createFilterRoute(array $resource)
    {
        $path = sprintf('/api/{_locale}{_S}%s/filter{_dot}{_format}', $resource['name']);
        $defaults = [
            '_controller' => ApiController::class.'::apiAction',
            '_locale' => $this->defaultLocale,
            '_entity' => $resource['entity'],
            '_strategy' => $resource['strategies']['filter'],
            '_request_processor' => $resource['request_processor'],
            '_sort' => $resource['default_sort'],
            '_sort_direction' => $resource['default_sort_direction'],
            '_resource_name' => $resource['name'],
            '_format' => 'json'
        ];
        $requirements = [
            '_S' => '/?',
            '_dot' => '\.?',
            '_locale' => '|'.implode('|', $this->locales),
            '_format' => 'json|xml'
        ];
        $route = new Route($path, $defaults, $requirements, [], '', [], ['GET']);

        return $route;
    }

    private function createItemRoute(array $resource)
    {
        $path = sprintf('/api/{_locale}{_S}%s/{id}{_dot}{_format}', $resource['name']);
        $defaults = [
            '_controller' => ApiController::class.'::apiAction',
            '_locale' => $this->defaultLocale,
            '_entity' => $resource['entity'],
            '_strategy' => $resource['strategies']['item'],
            '_request_processor' => $resource['request_processor'],
            '_resource_name' => $resource['name'],
            '_format' => 'json'
        ];
        $requirements = [
            '_S' => '/?',
            '_dot' => '\.?',
            '_locale' => '|'.implode('|', $this->locales),
            '_format' => 'json|xml',
            'id' => '\d+'
        ];
        $route = new Route($path, $defaults, $requirements, [], '', [], ['GET']);

        return $route;
    }

    private function createUpdateRoute(array $resource)
    {
        $path = sprintf('/api/{_locale}{_S}%s/{id}{_dot}{_format}', $resource['name']);
        $defaults = [
            '_controller' => ApiController::class.'::apiAction',
            '_locale' => $this->defaultLocale,
            '_entity' => $resource['entity'],
            '_strategy' => $resource['strategies']['update'],
            '_request_processor' => $resource['request_processor'],
            '_resource_name' => $resource['name'],
            '_format' => 'json'
        ];
        $requirements = [
            '_S' => '/?',
            '_dot' => '\.?',
            '_locale' => '|'.implode('|', $this->locales),
            '_format' => 'json|xml',
            'id' => '\d+'
        ];
        $route = new Route($path, $defaults, $requirements, [], '', [], ['PUT']);

        return $route;
    }
    private function createNewRoute(array $resource)
    {
        $path = sprintf('/api/{_locale}{_S}%s{_dot}{_format}', $resource['name']);
        $defaults = [
            '_controller' => ApiController::class.'::apiAction',
            '_locale' => $this->defaultLocale,
            '_entity' => $resource['entity'],
            '_strategy' => $resource['strategies']['create'],
            '_request_processor' => $resource['request_processor'],
            '_resource_name' => $resource['name'],
            '_format' => 'json'
        ];
        $requirements = [
            '_S' => '/?',
            '_dot' => '\.?',
            '_locale' => '|'.implode('|', $this->locales),
            '_format' => 'json|xml',
            'id' => '\d+'
        ];
        $route = new Route($path, $defaults, $requirements, [], '', [], ['POST']);

        return $route;
    }

    private function createDeleteRoute(array $resource)
    {
        $path = sprintf('/api/{_locale}{_S}%s/{id}{_dot}{_format}', $resource['name']);
        $defaults = [
            '_controller' => ApiController::class.'::apiAction',
            '_locale' => $this->defaultLocale,
            '_entity' => $resource['entity'],
            '_strategy' => $resource['strategies']['delete'],
            '_request_processor' => $resource['request_processor'],
            '_resource_name' => $resource['name'],
            '_format' => 'json',
        ];
        $requirements = [
            '_S' => '/?',
            '_dot' => '\.?',
            '_locale' => '|'.implode('|', $this->locales),
            '_format' => 'json|xml',
            'id' => '\d+'
        ];
        $route = new Route($path, $defaults, $requirements, [], '', [], ['DELETE']);

        return $route;
    }

    private function createMyRoute(array $resource)
    {
        $path = sprintf('/api/{_locale}{_S}my/%s/{id}{_dot}{_format}', $resource['name']);
        $defaults = [
            '_controller' => ApiController::class.'::apiAction',
            '_locale' => $this->defaultLocale,
            '_entity' => $resource['entity'],
            '_strategy' => $resource['strategies']['my'],
            '_request_processor' => $resource['request_processor'],
            '_resource_name' => $resource['name'],
            '_format' => 'json'
        ];
        $requirements = [
            '_S' => '/?',
            '_dot' => '\.?',
            '_locale' => '|'.implode('|', $this->locales),
            '_format' => 'json|xml',
            'id' => '\d+'
        ];
        $route = new Route($path, $defaults, $requirements, [], '', [], ['DELETE']);

        return $route;
    }
}
