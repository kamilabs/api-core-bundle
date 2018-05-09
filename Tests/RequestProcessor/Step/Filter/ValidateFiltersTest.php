<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\Filter\ValidateFilters;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;

class ValidateFiltersTest extends TestCase
{
    public function testRequiresBefore()
    {
        $step = new ValidateFilters();
        $this->assertEquals([], $step->requiresBefore());
    }

    public function testExecute()
    {

        $step = new ValidateFilters();
        $request = new Request();
        $step->setRequest($request);
        $step->setPreviousResponse(new ProcessorResponse($request, []));

        $response = $step->execute();
        $this->assertInstanceOf(ProcessorResponse::class, $response);
        $this->assertArrayHasKey('filters', $response->getData());
    }
}
