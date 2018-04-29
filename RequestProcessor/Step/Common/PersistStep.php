<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMException;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PersistStep extends AbstractStep
{
    /**
     * @var EntityManager
     */
    protected $manager;

    public function execute()
    {
        $entity = $this->getFromResponse('entity');

        try {
            $this->manager->persist($entity);
            $this->manager->flush();
        } catch (ORMException $exception) {
            throw new BadRequestHttpException('Your request can not be stored', $exception);
        }

        $this->createResponse(['response_data' => $entity], true, 200);
    }

    public function setDoctrine(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function requiresBefore()
    {
        return [ValidateFormStep::class, GetEntityFromReflectionStep::class];
    }


}