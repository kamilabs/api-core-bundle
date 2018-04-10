# KamiApiCoreBundle

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
    resource: "@KamiApiCoreBundle/Resources/config/routing.yml"
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


> #### Note! You must clear your cache after modifying your resources 

### Access rules
You have to define access rules in your entity using annotations.
By default all resources have restricted access. You must explicitly grant
access to each user role. 

  
### Form generation
Bundle will generate forms for both `POST` and `PUT` actions. See `@CanBeCreatedBy`, 
`@CanBeEditedBy`, `@AnonymousCreate`, `@AnonymousEdit` and `@Form` in annotation reference.  

### Request body converter

Most frontend libraries send form data as json. Bundle converts this payload
and injects parameters to request data 

## Extending
Extending API is the easiest part here. No special actions required. Just create 
additional routes and controllers.

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
 * @Api\AnonymousEdit
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
 * @Api\CanBeEditedBy({"ROLE_USER", "ROLE_ADMIN"})
 */
class YourEntity
{
    ...
        
    /**
     * @Api\CanBeEditedBy({"ROLE_USER", "ROLE_ADMIN"})
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