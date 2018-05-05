<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class FetchEntityByIdStep extends AbstractStep
{
    /**
     * @var Registry
     */
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function execute()
    {
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');
        $entity = $this->doctrine->getRepository($reflection->getName())->find($this->request->get('id', 0));

        if (!$entity) {
            throw new NotFoundHttpException('Resource not found');
        }

        return $this->createResponse(['entity' => $entity]);
    }

    public function requiresBefore()
    {
        return [GetReflectionFromRequestStep::class];
    }

}