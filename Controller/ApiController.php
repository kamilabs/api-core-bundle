<?php

namespace Kami\ApiCoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function apiAction(Request $request)
    {
        $strategy = $this->getParameter($request->attributes->get('_strategy'));
        $requestProcessor = $this->get($request->attributes->get('_request_processor'));

        return $requestProcessor->executeStrategy(
            $this->get('kami.api_core.strategy_factory')->create($strategy),
            $request
        )->toHttpResponse();
    }
}
