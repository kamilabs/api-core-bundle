<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetEntityFromReflectionStep;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class GetEntityFromReflectionStepTest extends TestCase
{

    public function testExecute()
    {
        $step = new GetEntityFromReflectionStep();
        $step->setArtifacts(new ArtifactCollection([
            new Artifact('reflection', new \ReflectionClass(Entity::class)),
            new Artifact('access_granted', new \ReflectionClass(Entity::class))
        ]));

        $artifacts = $step->execute(new Request());
        $this->assertInstanceOf(ArtifactCollection::class, $artifacts);
        $this->assertInstanceOf(Entity::class, $artifacts->get('entity')->getValue());
    }
    
    public function testGetRequiredArtifacts()
    {
        $step = new GetEntityFromReflectionStep();
        $this->assertEquals(['reflection', 'access_granted'], $step->getRequiredArtifacts());
    }
}
