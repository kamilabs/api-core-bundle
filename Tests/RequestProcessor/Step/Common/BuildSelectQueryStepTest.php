<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\Common\Annotations\Reader;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class BuildSelectQueryStepTest extends TestCase
{

    public function testCanBeConstructed()
    {
        $accessManager = $this->createMock(AccessManager::class);
        $reader = $this->createMock(Reader::class);

        $step = new BuildSelectQueryStep($accessManager, $reader);
        $this->assertInstanceOf(BuildSelectQueryStep::class, $step);
    }

    public function testGetRequiredArtifacts()
    {
        $accessManager = $this->createMock(AccessManager::class);
        $reader = $this->createMock(Reader::class);

        $step = new BuildSelectQueryStep($accessManager, $reader);
        $this->assertEquals(['reflection', 'query_builder', 'access_granted'], $step->getRequiredArtifacts());
    }

    public function testExecute()
    {
        $readerMock = $this->createMock(Reader::class);
        $accessManagerMock = $this->createMock(AccessManager::class);
        $request = new Request();
        $step = new BuildSelectQueryStep($accessManagerMock, $readerMock);

        $step->setArtifacts(new ArtifactCollection([
            new Artifact('reflection', new \ReflectionClass(MyModel::class)),
            new Artifact('access_granted', true),
            new Artifact('query_builder', new QueryBuilder($this->createMock(EntityManager::class)))
        ]));

        $artifacts = $step->execute($request);
        $this->assertInstanceOf(ArtifactCollection::class, $artifacts);
        $this->assertTrue(true, $artifacts->get('select_query_built'));
    }
}
