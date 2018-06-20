<?php

namespace Kami\ApiCoreBundle\Tests\RequestProcessor\Step\Common;

use Doctrine\Common\EventManager;
use Doctrine\Common\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kami\ApiCoreBundle\Model\Pageable;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\BuildSelectQueryStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetEntityFromReflectionStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\GetReflectionFromRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\HandleRequestStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\PaginateStep;
use Kami\ApiCoreBundle\Tests\Entity\MyModel;
use Kami\ApiCoreBundle\Tests\fixtures\Entity;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaginateStepTest extends TestCase
{

    public function testGetRequiredArtifacts()
    {
        $step = new PaginateStep(10, 100);
        $this->assertEquals(['query_builder', 'select_query_built', 'access_granted'], $step->getRequiredArtifacts());
    }
}
