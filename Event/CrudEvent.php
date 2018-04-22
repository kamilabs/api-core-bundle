<?php

namespace Kami\ApiCoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CrudEvent
 *
 * @package Kami\ApiCoreBundle\Event
 */
class CrudEvent extends Event
{
    /**
     * @var \ReflectionClass
     */
    protected $reflection;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var object
     */
    protected $data;

    /**
     * CrudEvent constructor.
     * @param \ReflectionClass $reflection
     * @param Request $request
     * @param null|Response $response
     * @param null|mixed $data
     */
    public function __construct(\ReflectionClass $reflection, Request $request, $response = null, $data = null)
    {
        $this->reflection = $reflection;
        $this->request = $request;
        $this->response = $response;
        $this->data = $data;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * @return null|Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return null|object
     */
    public function getData()
    {
        return $this->data;
    }
}