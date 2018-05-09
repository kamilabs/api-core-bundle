<?php

namespace src\Kami\ApiCoreBundle\Routing;

use Kami\ApiCoreBundle\Routing\ApiCoreRoutingLoader;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RouteCollection;

class ApiCoreLoaderTest extends WebTestCase
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
        $loader = new ApiCoreRoutingLoader(
            [
                [
                    'entity' => MyModel::class,
                    'name' => 'resource',
                    'strategies' => [
                        'index'  => 'test',
                        'item'   => 'test',
                        'filter' => 'test',
                        'create' => 'test',
                        'update' => 'test',
                        'delete' => 'test'
                    ],
                    'request_processor' => 'test',
                    'default_sort' => 'test',
                    'default_sort_direction' => 'test'
                ]
            ],
            ['en'],
            ['en']
        );

        $collection = $loader->load('');

        $this->assertInstanceOf(RouteCollection::class, $collection);
        $this->assertCount(6, $collection);
    }
}