<?php

namespace Kami\ApiCoreBundle\RequestProcessing\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface RequestProcessorInterface
{
    /**
     * @return Response
     */
    public function getIndex(Request $request);

    /**
     * @return Response
     */
    public function getSingle(Request $request);

    /**
     * @return Response
     */
    public function filter(Request $request);

    /**
     * @return Response
     */
    public function create(Request $request);

    /**
     * @return Response
     */
    public function update(Request $request);

    /**
     * @return Response
     */
    public function delete(Request $request);
}