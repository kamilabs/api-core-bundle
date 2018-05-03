<?php

namespace Kami\ApiCoreBundle\Tests\fixtures;

use Kami\ApiCoreBundle\Annotation as Api;

/**
 * Class Entity
 * @package Kami\ApiCoreBundle\Tests\fixtures
 *
 * @Api\AnonymousAccess
 */
class Entity
{
    private $id;

    private $canBeReadByAnon;

    private $canBeReadByUser;

    private $canBeReadByAdmin;

    private $canBeCreatedByAnon;

    private $canBeCreatedAsUser;

    private $canBeCreatedAsAdmin;

    private $canBeUpdatedAsAnon;

    private $canBeUpdatedAsUser;

    private $canBeUpdatedAsAdmin;
}