<?php

namespace src\Kami\ApiCoreBundle\Routing;

use Kami\ApiCoreBundle\Routing\ApiCoreRoutingLoader;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouteCollection;

class ApiLoaderTest extends WebTestCase
{
// can be constructed with necessary params

    public function testCanBeConstructedWithNecessaryParams()
    {
        $apiLoader = new ApiCoreRoutingLoader(
            [], [], 'en'
        );
        $this->assertInstanceOf(ApiCoreRoutingLoader::class, $apiLoader);
    }

    // load

    public function testLoad()
    {
        $loader = new ApiCoreRoutingLoader([0 => ['name' => 'my-model', 'entity' => 'Kami\ApiCoreBundle\Resources\Fixtures\Entity\MyModel']], [], 'en');
        $this->assertInstanceOf(RouteCollection::class, $loader->load(''));
    }
}