<?php

namespace Kami\ApiCoreBundle\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Kami\ApiCoreBundle\Annotation as Api;

/**
 * MyModel
 *
 * @ORM\Table(name="test")
 * @ORM\Entity(repositoryClass="Kami\ApiCoreBundle\Tests\Repository\TestRepository")
 * @Api\AnonymousAccess()
 * @Api\Access({"ROLE_ADMIN", "ROLE_USER"})
 * @Api\CanBeCreatedBy({"ROLE_ADMIN", "ROLE_USER"})
 * @Api\CanBeEditedBy({"ROLE_ADMIN"})
 */
class MyModel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank()
     * @Api\Access({"ROLE_ADMIN", "ROLE_USER"})
     * @Api\CanBeEditedBy({"ROLE_ADMIN"})
     */
    private $title;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return MyModel
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}