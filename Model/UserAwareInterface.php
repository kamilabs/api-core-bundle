<?php

namespace Kami\ApiCoreBundle\Model;


use Symfony\Component\Security\Core\User\UserInterface;

interface UserAwareInterface
{
    public function getUser() : UserInterface;

    public function setUser(UserInterface $user) : UserInterface;
}