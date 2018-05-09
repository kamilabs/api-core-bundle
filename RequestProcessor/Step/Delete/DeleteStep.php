<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Delete;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\FetchEntityByIdStep;
use Kami\ApiCoreBundle\RequestProcessor\Step\Common\ValidateResourceAccessStep;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DeleteStep extends AbstractStep
{
    /**
     * @var EntityManager
     */
    protected  $manager;

    public function __construct(Registry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }


    public function execute()
    {
        $entity = $this->getFromResponse('entity');

        try {
            $this->manager->remove($entity);
            $this->manager->flush();
        } catch (ORMException $exception) {
            throw new BadRequestHttpException('Your request can not be processed', $exception);
        }

        return $this->createResponse(['response_data' => null], true, 204);
    }

    public function requiresBefore()
    {
        return [ValidateResourceAccessStep::class, FetchEntityByIdStep::class];
    }

}