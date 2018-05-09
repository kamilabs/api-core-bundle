<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step;

use Kami\ApiCoreBundle\RequestProcessor\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * StepInterface
 *
 * @package Kami\ApiCoreBundle\RequestProcessor\Strategy
 */
interface StepInterface
{
    /**
     * @return ResponseInterface
     */
    public function execute();

    /**
     * @return array
     */
    public function requiresBefore();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param ResponseInterface $response
     *
     * @return StepInterface
     */
    public function setPreviousResponse(ResponseInterface $response);

    /**
     * @param Request $request
     *
     * @return StepInterface
     */
    public function setRequest(Request $request);
}