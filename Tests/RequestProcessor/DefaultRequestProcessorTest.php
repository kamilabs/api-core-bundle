<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\RequestProcessor\DefaultRequestProcessor;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetQueryBuilderStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\SerializeResponseDataStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\SortStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\StepInterface;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use const true;

class DefaultRequestProcessorTest extends TestCase
{

    public function testExecuteStrategy()
    {
        $step = $this->createMock(StepInterface::class);
        $step->expects($this->any())
            ->method('execute')
            ->willReturn(new ProcessorResponse(new Request(), ['response_data' => 123], true));
        $step->expects($this->any())
            ->method('requiresBefore')
            ->willReturn([]);

        $defaultRequestProcessor = new DefaultRequestProcessor();
        $defaultRequestProcessor->addStep('test_step', $step);

        $this->assertInstanceOf(
            Response::class,
            $defaultRequestProcessor->executeStrategy(['test_step'], new Request())
        );
    }
}