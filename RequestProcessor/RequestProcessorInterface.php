<?php

namespace Kami\ApiCoreBundle\RequestProcessor;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestProcessorInterface
{
    /**
     * @param array $strategy
     * @param Request $request
     * @return Response
     */
    public function executeStrategy(array $strategy, Request $request);

}