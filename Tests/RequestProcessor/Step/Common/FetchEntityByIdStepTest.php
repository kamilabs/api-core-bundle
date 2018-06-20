<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\FetchEntityByIdStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Kami\ApiCoreBundle\Tests\Repository\TestRepository;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FetchEntityByIdStepTest extends TestCase
{

    public function testExecute()
    {
        $repositoryMock = $this->createMock(TestRepository::class);
        $repositoryMock->expects($this->at(0))->method('find')->willReturn(new MyModel());
        $registry = $this->createMock(Registry::class);
        $registry->expects($this->at(0))->method('getRepository')->willReturn($repositoryMock);
        $request = new Request();
        $request->attributes->set('id', 1);
        $step = new FetchEntityByIdStep($registry);
        $step->setArtifacts(new ArtifactCollection([
            new Artifact('reflection', new \ReflectionClass(MyModel::class)),
            new Artifact('access_granted', true)
        ]));

        $artifacts = $step->execute($request);
        
        $this->assertInstanceOf(ArtifactCollection::class, $artifacts);
        $this->assertInstanceOf(MyModel::class, $artifacts->get('entity')->getValue());
    }

    public function testExecuteFailure()
    {
        $repositoryMock = $this->createMock(TestRepository::class);
        $doctrineMock = $this->createMock(Registry::class);
        $doctrineMock->expects($this->at(0))->method('getRepository')->willReturn($repositoryMock);
        $request = new Request();
        $step = new FetchEntityByIdStep($doctrineMock);
        $step->setArtifacts(new ArtifactCollection([
            new Artifact('reflection', new \ReflectionClass(MyModel::class)),
            new Artifact('access_granted', true)
        ]));

        $this->expectException(NotFoundHttpException::class);
        $step->execute($request);
    }

    public function testGetRequiredArtifacts()
    {
        $step = new FetchEntityByIdStep($this->createMock(Registry::class));
        $this->assertEquals(['reflection', 'access_granted'], $step->getRequiredArtifacts());
    }
}
