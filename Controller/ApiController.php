<?php

namespace Kami\ApiCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->get('kami_api_core.api_manager')->getIndex($request);
    }

    public function filterAction(Request $request)
    {
        return $this->get('kami_api_core.api_manager')->filter($request);
    }

    public function itemAction(Request $request)
    {
        return $this->get('kami_api_core.api_manager')->getSingleResource($request);
    }

    public function newAction(Request $request)
    {
        return $this->get('kami_api_core.api_manager')->createResource($request);
    }

    public function updateAction(Request $request)
    {
        return $this->get('kami_api_core.api_manager')->editResource($request);
    }

    public function deleteAction(Request $request)
    {
        return $this->get('kami_api_core.api_manager')->deleteResource($request);
    }
}
