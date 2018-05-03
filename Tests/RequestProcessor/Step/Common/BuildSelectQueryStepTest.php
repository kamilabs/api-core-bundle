<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetQueryBuilderStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use PHPUnit\Framework\TestCase;

class BuildSelectQueryStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $accessManager = $this->createMock(AccessManager::class);
        $step = new BuildSelectQueryStep($accessManager);
        $this->assertInstanceOf(BuildSelectQueryStep::class, $step);
    }

    public function testRequiresBefore()
    {
        $accessManager = $this->createMock(AccessManager::class);
        $step = new BuildSelectQueryStep($accessManager);
        $this->assertEquals([GetQueryBuilderStep::class], $step->requiresBefore());
    }

    public function testExecute()
    {

    }
}
