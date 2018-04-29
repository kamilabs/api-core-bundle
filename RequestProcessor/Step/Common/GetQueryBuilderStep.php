<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;

class GetQueryBuilderStep extends AbstractStep
{
    /**
     * @var Registry
     */
    private $doctrine;

    public function execute()
    {
        return $this->createResponse(['query_builder' => $this->doctrine->getManager()->createQueryBuilder()]);
    }

    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function requiresBefore()
    {
        return [
            GetReflectionFromRequestStep::class,
            ValidateResourceAccessStep::class
        ];
    }
}