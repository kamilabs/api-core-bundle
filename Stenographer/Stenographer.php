<?php


namespace Kami\ApiCoreBundle\Stenographer;


use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\Mapping\Column;
use EXSyst\Component\Swagger\Path;
use EXSyst\Component\Swagger\Swagger;
use Kami\ApiCoreBundle\Annotation\Access;
use Kami\ApiCoreBundle\Annotation\AnonymousAccess;
use Kami\ApiCoreBundle\Annotation\AnonymousCreate;
use Kami\ApiCoreBundle\Annotation\AnonymousUpdate;
use Kami\ApiCoreBundle\Annotation\CanBeCreatedBy;
use Kami\ApiCoreBundle\Annotation\CanBeUpdatedBy;
use Symfony\Component\Routing\Route;

class Stenographer
{
    /**
     * @var Reader
     */
    protected $reader;

    /**
     * Stenographer constructor.
     *
     * @param Reader $reader
     */
    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param Swagger $api
     *
     * @param Route $route
     */
    public function getStenography(Swagger $api, Route $route)
    {
        $paths = $api->getPaths();
        $paths->set(sprintf('/api/%s', $route->getDefault('_resource_name')), new Path([
            'get' => [
                'summary' => sprintf('Get %s index', $route->getDefault('_resource_name')),
                'parameters' => [
                    [
                        'name' => 'page',
                        'description' => 'Page to return',
                        'in' => 'integer'
                    ],
                    [
                        'name' => 'sort',
                        'description' => 'Field to sort',
                        'in' => 'string'
                    ],
                    [
                        'name' => 'direction',
                        'description' => 'Sort direction',
                        'in' => 'string (asc|desc)'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Successful operation',
                    ],
                    '403' => [
                        'description' => 'Access denied'
                    ],
                    '401' => [
                        'description' => 'Authorization required'
                    ]
                ],
                'tags' => [$route->getDefault('_resource_name')]
            ],
            'post' => [
                'summary' => sprintf('Create %s', $route->getDefault('_resource_name')),
                'parameters' => $this->getCreateFormFields($route->getDefault('_entity')),
                'tags' => [$route->getDefault('_resource_name')],
                'responses' => [
                    '200' => [
                        'description' => 'Successful operation',
                    ],
                    '403' => [
                        'description' => 'Access denied'
                    ],
                    '401' => [
                        'description' => 'Authorization required'
                    ]
                ],
            ]
        ]));
        $paths->set(sprintf('/api/%s/{id}', $route->getDefault('_resource_name')), new Path([
            'get' => [
                'summary' => sprintf('Get single %s', $route->getDefault('_resource_name')),
                'parameters' => [
                    [
                        'name' => 'id',
                        'description' => 'Resource identifier',
                        'in' => 'integer'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Successful operation'
                    ],
                    '403' => [
                        'description' => 'Access denied'
                    ],
                    '401' => [
                        'description' => 'Authorization required'
                    ]
                ],
                'tags' => [$route->getDefault('_resource_name')]
            ],
            'put' => [
                'summary' => sprintf('Update %s', $route->getDefault('_resource_name')),
                'parameters' => array_merge(
                    [[
                        'name' => 'id',
                        'description' => 'Resource identifier',
                        'in' => 'integer'
                    ]],
                    $this->getUpdateFormFields($route->getDefault('_entity'))),
                'tags' => [$route->getDefault('_resource_name')]
            ],
            'delete' => [
                'summary' => sprintf('Delete %s', $route->getDefault('_resource_name')),
                'parameters' => [
                    [
                        'name' => 'id',
                        'description' => 'Resource identifier',
                        'in' => 'integer'
                    ]
                ],
                'responses' => [
                    '204' => [
                        'description' => 'Successful operation'
                    ],
                    '403' => [
                        'description' => 'Access denied'
                    ],
                    '401' => [
                        'description' => 'Authorization required'
                    ]
                ],
                'tags' => [$route->getDefault('_resource_name')]
            ]
        ]));
        $paths->set(sprintf('/api/%s/filter', $route->getDefault('_resource_name')), new Path([
            'get' => [
                'summary' => 'Filter operation',
                'description' => "Filters payload should be base64 encoded json string\n Available filters are:\n"
                    ."|Parameter|Access|Type|\n"
                    ."|---------|------|----|\n".
                    implode("\n", $this->getAvailableFilterParams($route->getDefault('_entity'))),
                'parameters' => [
                    [
                        'name' => 'page',
                        'description' => 'Page to return',
                        'in' => 'integer'
                    ],
                    [
                        'name' => 'sort',
                        'description' => 'Field to sort',
                        'in' => 'string'
                    ],
                    [
                        'name' => 'direction',
                        'description' => 'Sort direction',
                        'in' => 'string (asc|desc)'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'Successful operation',
                    ],
                    '403' => [
                        'description' => 'Access denied'
                    ],
                    '401' => [
                        'description' => 'Authorization required'
                    ]
                ],
                'tags' => [$route->getDefault('_resource_name')]
            ],
        ]));
    }


    protected function getAvailableFilterParams($entity)
    {
        $reflection = $this->getEntityReflection($entity);
        $availableParams = [];

        foreach ($reflection->getProperties() as $property) {
            $anonymousAccess = $this->reader->getPropertyAnnotation($property, AnonymousAccess::class);
            $access = $this->reader->getPropertyAnnotation($property, Access::class);
            $column = $this->reader->getPropertyAnnotation($property, Column::class)
            if (($anonymousAccess || $access) && $column) {
                $param = ['name' => $property->getName()];
                $param['access'] = $anonymousAccess ? ['Any'] : [];
                $param['access'] = $access ? $access->roles : $param['access'];
                $param['access'] = implode(' ', $param['access']);
                $param['type'] = $column->type;
                $availableParams[] = sprintf('|%s|%s|%s|', $param['name'], $param['access'], $param['type']);
            }
        }

        return $availableParams;
    }

    public function getCreateFormFields($entity)
    {
        $reflection = $this->getEntityReflection($entity);
        $availableParams = [];

        foreach ($reflection->getProperties() as $property) {
            $anonymousCreate = $this->reader->getPropertyAnnotation($property, AnonymousCreate::class);
            $canBeCreatedBy = $this->reader->getPropertyAnnotation($property, CanBeCreatedBy::class);
            if ($anonymousCreate || $canBeCreatedBy) {
                $param = ['name' => sprintf('%s[%s]',
                    Inflector::tableize($reflection->getShortName()),
                    $property->getName()
                )];
                $param['in'] = $this->reader->getPropertyAnnotation($property, Column::class)->type;
                $availableParams[] = $param;
            }
        }

        return $availableParams;
    }

    public function getUpdateFormFields($entity)
    {
        $reflection = $this->getEntityReflection($entity);
        $availableParams = [];

        foreach ($reflection->getProperties() as $property) {
            $anonymousUpdate = $this->reader->getPropertyAnnotation($property, AnonymousUpdate::class);
            $canBeUpdatedBy = $this->reader->getPropertyAnnotation($property, CanBeUpdatedBy::class);
            if ($canBeUpdatedBy || $anonymousUpdate) {
                $param = ['name' => sprintf('%s[%s]',
                    Inflector::tableize($reflection->getShortName()),
                    $property->getName()
                )];
                $column = $this->reader->getPropertyAnnotation($property, Column::class);

                $param['in'] = $column ? $column->type : 'Unknown'; // todo: refactor this, so you can generate proper doc
                $availableParams[] = $param;
            }
        }

        return $availableParams;
    }

    /**
     * @param string $entity
     * @return \ReflectionClass
     */
    protected function getEntityReflection($entity)
    {
        try {
            $reflection = new \ReflectionClass($entity);
        } catch (\ReflectionException $e) {
            throw new \InvalidArgumentException('Route default entity can not be found');
        }
        return $reflection;
    }
}
