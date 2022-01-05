<?php

namespace Kami\ApiCoreBundle\Controller;

use Kami\ApiCoreBundle\RequestProcessor\ProcessorRegistry;
use Kami\ApiCoreBundle\RequestProcessor\StrategyFactory;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    private $processorRegistry;

    private $strategyFactory;

    public function __construct(ProcessorRegistry $processorRegistry, StrategyFactory $strategyFactory)
    {
        $this->processorRegistry = $processorRegistry;
        $this->strategyFactory = $strategyFactory;
    }


    public function apiAction(Request $request)
    {
        $strategy = $request->attributes->get('_strategy');
        $requestProcessor = $this->processorRegistry->getProcessor($request->attributes->get('_request_processor'));
        
        return $requestProcessor->executeStrategy(
            $this->strategyFactory->create($strategy),
            $request
        )->toHttpResponse();
    }
}
