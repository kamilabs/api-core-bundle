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
     * StepInterface constructor.
     *
     * @param Request $request
     * @param ResponseInterface $response
     */
    public function __construct(Request $request, ResponseInterface $response);

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

}