# KamiApiCoreBundle [![Build Status](https://travis-ci.org/kamilabs/api-core-bundle.svg?branch=master)](https://travis-ci.org/kamilabs/api-core-bundle)

This bundle provides easiest way to create CRUD actions in 
your REST applications. Simple and flexible configuration for 
each resource included.

## Installation
Installation is easy. You just follow these sipmle steps 

Require it
```bash
composer require kami/api-core-bundle
```

Add it to your kernel
```php
<?php

// AppKernel.php

    public function registerBundles()
    {
        $bundles = [
            ...
            new Kami\ApiCoreBundle\KamiApiCoreBundle(),
            ...
        ];
    }
```

## Configuration
Add your resources
```yaml
# app/config/config.yml

kami_api_core:
  locales: ['en', 'de']
  resources:
    - { name: your-resource-name, entity: AppBundle\Entity\YourEntiy }
```
Add KamiApiCore routing loader
```yaml
kami_api_core:
    resource: '@KamiApiCoreBundle/Resources/config/routing.xml'
```

Now you are good to go.

## Workflow

### Routing loader
Bundle will generate 5 routes for each resource you specified in your config
* `GET /api/your-resource-name` - Index route
* `GET /api/your-resource-name/{id}` - Get single resource
* `GET /api/your-resource-name/filter` - Filter resource
* `POST /api/your-resource-name` - Create resource
* `PUT /api/your-resource-name/{id}` - Update resource
* `DELETE /api/your-resource-name/{id}` - Delete resource

Optionally if resource entity implements `KamiApiCoreBundle\Model\UserAwareInterface`  additonal route will be generated

* `GET /api/my/your-resource-name` - Get resources belonged to current user 

> #### Note! You must clear your cache after modifying your resources 

### Access rules
You have to define access rules in your entity using annotations.
By default all resources have restricted access. You must explicitly grant
access to each user role. 

### Strategies

Bundle utilizes [Strategy pattern](https://en.wikipedia.org/wiki/Strategy_pattern) and has default strategy for each route
You can override any of strategy by resource configuration:

#### Example
```yaml
# app/config/config.yml

kami_api_core:
  locales: ['en', 'de']
  resources:
      - name: your-resource-name 
        entity: AppBundle\Entity\YourEntiy 
        strategies:
            index:  "%my_awesome_index_strategy%"
            filter: "%my_awesome_filter_strategy%" 
            item:   "%my_awesome_item_strategy%"
            create: "%my_awesome_create_strategy%"
            update: "%my_awesome_update_strategy%"
            delete: "%my_awesome_delete_strategy%"

```
### Default strategies
Default strategies are using following steps:

#### Index
* get_reflection_from_request
* validate_resource_access
* get_query_builder
* build_select_query
* sort
* paginate
* serialize_response_data
#### Item
* get_reflection_from_request
* validate_resource_access
* get_query_builder
* build_select_query
* item_add_where
* item_set_selected_data
* serialize_response_data
#### Create
* get_reflection_from_request
* validate_resource_access
* get_entity_from_reflection
* build_create_form
* handle_request
* validate_form
* persist
* trim_response
* serialize_response_data
#### Update
* get_reflection_from_request
* validate_resource_access
* fetch_entity_by_id
* build_update_form
* handle_request
* validate_form
* persist
* trim_response
* serialize_response_data
#### Delete
* get_reflection_from_request
* validate_resource_access
* fetch_entity_by_id
* delete
#### Filter
* get_reflection_from_request
* validate_resource_access
* get_query_builder
* build_select_query
* validate_filters
* filter
* sort
* paginate
* serialize_response_data

### Adding custom steps
To add custom step just extend `Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep`

**Example:**
```php
<?php 

namespace Acme\AppBundle\Step;

use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;

class MyStep extends AbstractStep
{
    public function execute()
    {
        // Do your stuff here
        
        return $this->createResponse(['your_data' => $data]);
    }

    public function requiresBefore()
    {
        return [MyStep::class];
    }
}
```
```yaml
# services.yml

my_awesome_step: 
    class: Acme\AppBundle\Step\MyStep
    arguments:
      - "@service"
      - ...
    tags: 
      - { name: kami_api_core.strategy_step, shortcut: "my_step" }    
        
```


### Form generation
Default strategy will generate forms for both `POST` and `PUT` actions.
Only accessible fields for current user will be included.
 
See `@CanBeCreatedBy`, `@CanBeEditedBy`, `@AnonymousCreate`, `@AnonymousEdit` and `@Form` in annotation reference.  

### Request body converter

Most frontend libraries send form data as json. Bundle converts this payload
and injects parameters to request data 

### Filters
`GET /api/your-resource-name/filter` endpoint accepts query param `filter`, 
it should contain base64 encoded json payload with applied filters

#### Available filters with examples
Available filters are:
* `eq` - Equals
* `gt` - Greater than
* `lt` - Lower than
* `lk` - Like
* `bw` - Between
##### Examples
```json
// Equals
{"type": "eq", "property": "id", "value": 1}]
// Greater than
{"type": "gt", "property": "id", "value": 3}]
// Lower than
{"type": "lt", "property": "id", "value": 3}]
// Like
{"type": "lk", "property": "title", "value": "foo"}]
// Between
{"type": "bw", "property": "id", "min": 1, "max": 5}]
```
### Sort

You can sort `index` and `filter` route responses using `sort` and `direction` query params.
`sort` parameter should represent field of your entity, while `direction` can be either `asc` or `desc`
#### Example
```
GET /api/your-resource-name?sort=property&direction=desc
```

## Annotations reference

### @Access
Defines roles that can access the resource or property

_Usage example_
```php
<?php

namespace AppBundle\Entity;

use Kami\ApiCoreBundle\Annotation as Api;

class YourEntity
{
    ...
        
    /**
     * @Access({"ROLE_USER", "ROLE_ADMIN"})
     * @ORM\Column(name="property", type="string", length=255)
     */
    private $property;
    
    ...
}
```
### @AnonymousAccess
Defines anonymous access to the resource

_Usage example_
```php
<?php

namespace AppBundle\Entity;

use Kami\ApiCoreBundle\Annotation as Api;

/**
 * @Api\AnonymousAccess
 */
class YourEntity
{
    ...
        
    /**
     * @ORM\Column(name="property", type="string", length=255)
     * @Api\AnonymousAccess
     */
    private $property;
    
    ...
}
```
### @AnonymousCreate
Defines if anonymous users can create the resource

_Usage example_
```php
<?php

namespace AppBundle\Entity;

use Kami\ApiCoreBundle\Annotation as Api;

/**
 * @Api\AnonymousCreate
 */
class YourEntity
{
    ...
        
    /**
     * @ORM\Column(name="property", type="string", length=255)
     * @Api\AnonymousCreate 
     */
    private $property;
    
    ...
}
```

### @AnonymousEdit
Defines if anonymous users can edit the resource

_Usage example_
```php
<?php

namespace AppBundle\Entity;

use Kami\ApiCoreBundle\Annotation as Api;

/**
 * AnonymousUpdate
 */
class YourEntity
{
    ...
        
    /**
     * @ORM\Column(name="property", type="string", length=255)
     */
    private $property;
    
    ...
}
```

### @CanBeCreatedBy
Defines roles that can create the resource or property

_Usage example_
```php
<?php

namespace AppBundle\Entity;

use Kami\ApiCoreBundle\Annotation as Api;

/**
 * @Api\CanBeCreatedBy({"ROLE_USER", "ROLE_ADMIN"})
 */
class YourEntity
{
    ...
        
    /**
     * @Api\CanBeCreatedBy({"ROLE_USER", "ROLE_ADMIN"})
     * @ORM\Column(name="property", type="string", length=255)
     */
    private $property;
    
    ...
}
```
### @CanBeEditedBy

Defines roles that can update the resource or property

_Usage example_
```php
<?php

namespace AppBundle\Entity;

use Kami\ApiCoreBundle\Annotation as Api;

/**
 * CanBeUpdatedBy({"ROLE_USER", "ROLE_ADMIN"})
 */
class YourEntity
{
    ...
        
    /**
     * @Api\CanBeUpdatedBy({"ROLE_USER", "ROLE_ADMIN"})
     * @ORM\Column(name="property", type="string", length=255)
     */
    private $property;
    
    ...
}
```

### @Form

Used to define form options. Accepts two arguments: `type` and `options`. See 
 Symfony Form component [documentation](https://symfony.com/doc/current/forms.html#built-in-field-types)

_Usage example_
```php
<?php

namespace AppBundle\Entity;

use Kami\ApiCoreBundle\Annotation as Api;

class YourEntity
{
    ...
        
    /**
     * @Api\Form(type="Symfony\Component\Form\Extension\Core\Type\DateTimeType", options={"widget": "single_text"})
     * @ORM\Column(name="property", type="datetime")
     */
    private $property;
    
    ...
}
```