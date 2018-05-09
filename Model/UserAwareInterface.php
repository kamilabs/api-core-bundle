<?php

namespace Kami\ApiCoreBundle\Model;


use Symfony\Component\Security\Core\User\UserInterface;

interface UserAwareInterface
{
    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param UserInterface $user
     * @return UserAwareInterface
     */
    public function setUser(UserInterface $user);
}