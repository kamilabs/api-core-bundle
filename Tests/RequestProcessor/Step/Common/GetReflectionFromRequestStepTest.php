<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use Kami\Component\RequestProcessor\ArtifactCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class GetReflectionFromRequestStepTest extends TestCase
{

    public function testExecute()
    {
        $request = new Request();
        $request->attributes->set('_entity', Entity::class);
        $step = new GetReflectionFromRequestStep();
        $artifacts = $step->execute($request);
        $this->assertInstanceOf(ArtifactCollection::class, $artifacts);
        $this->assertInstanceOf(\ReflectionClass::class, $artifacts->get('reflection')->getValue());
    }

    public function testExecuteIfEntityNotExist()
    {
        $request = new Request();
        $request->attributes->set('_entity', 'Not\Existing\Class');
        $step = new GetReflectionFromRequestStep();
        $this->expectException(\ReflectionException::class);
        $step->execute($request);
    }

    public function testGetRequiredArtifacts()
    {
        $step = new GetReflectionFromRequestStep();
        $this->assertEquals([], $step->getRequiredArtifacts());
    }
}
