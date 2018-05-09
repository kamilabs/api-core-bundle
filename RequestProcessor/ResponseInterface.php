<?php

namespace Kami\ApiCoreBundle\RequestProcessor;

use Symfony\Component\HttpFoundation\Response;


interface ResponseInterface
{
    /**
     * @return Response
     */
    public function toHttpResponse();

    /**
     * @return array
     */
    public function getData();

    /**
     * @return int
     */
    public function getStatus();
}