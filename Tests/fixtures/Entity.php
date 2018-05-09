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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getCanBeReadByAnon()
    {
        return $this->canBeReadByAnon;
    }

    /**
     * @param mixed $canBeReadByAnon
     */
    public function setCanBeReadByAnon($canBeReadByAnon)
    {
        $this->canBeReadByAnon = $canBeReadByAnon;
    }

    /**
     * @return mixed
     */
    public function getCanBeReadByUser()
    {
        return $this->canBeReadByUser;
    }

    /**
     * @param mixed $canBeReadByUser
     */
    public function setCanBeReadByUser($canBeReadByUser)
    {
        $this->canBeReadByUser = $canBeReadByUser;
    }

    /**
     * @return mixed
     */
    public function getCanBeReadByAdmin()
    {
        return $this->canBeReadByAdmin;
    }

    /**
     * @param mixed $canBeReadByAdmin
     */
    public function setCanBeReadByAdmin($canBeReadByAdmin)
    {
        $this->canBeReadByAdmin = $canBeReadByAdmin;
    }

    /**
     * @return mixed
     */
    public function getCanBeCreatedByAnon()
    {
        return $this->canBeCreatedByAnon;
    }

    /**
     * @param mixed $canBeCreatedByAnon
     */
    public function setCanBeCreatedByAnon($canBeCreatedByAnon)
    {
        $this->canBeCreatedByAnon = $canBeCreatedByAnon;
    }

    /**
     * @return mixed
     */
    public function getCanBeCreatedAsUser()
    {
        return $this->canBeCreatedAsUser;
    }

    /**
     * @param mixed $canBeCreatedAsUser
     */
    public function setCanBeCreatedAsUser($canBeCreatedAsUser)
    {
        $this->canBeCreatedAsUser = $canBeCreatedAsUser;
    }

    /**
     * @return mixed
     */
    public function getCanBeCreatedAsAdmin()
    {
        return $this->canBeCreatedAsAdmin;
    }

    /**
     * @param mixed $canBeCreatedAsAdmin
     */
    public function setCanBeCreatedAsAdmin($canBeCreatedAsAdmin)
    {
        $this->canBeCreatedAsAdmin = $canBeCreatedAsAdmin;
    }

    /**
     * @return mixed
     */
    public function getCanBeUpdatedAsAnon()
    {
        return $this->canBeUpdatedAsAnon;
    }

    /**
     * @param mixed $canBeUpdatedAsAnon
     */
    public function setCanBeUpdatedAsAnon($canBeUpdatedAsAnon)
    {
        $this->canBeUpdatedAsAnon = $canBeUpdatedAsAnon;
    }

    /**
     * @return mixed
     */
    public function getCanBeUpdatedAsUser()
    {
        return $this->canBeUpdatedAsUser;
    }

    /**
     * @param mixed $canBeUpdatedAsUser
     */
    public function setCanBeUpdatedAsUser($canBeUpdatedAsUser)
    {
        $this->canBeUpdatedAsUser = $canBeUpdatedAsUser;
    }

    /**
     * @return mixed
     */
    public function getCanBeUpdatedAsAdmin()
    {
        return $this->canBeUpdatedAsAdmin;
    }

    /**
     * @param mixed $canBeUpdatedAsAdmin
     */
    public function setCanBeUpdatedAsAdmin($canBeUpdatedAsAdmin)
    {
        $this->canBeUpdatedAsAdmin = $canBeUpdatedAsAdmin;
    }

}