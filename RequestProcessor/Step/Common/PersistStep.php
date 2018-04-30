<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Doctrine\Bundle\DoctrineBundle\Registry;
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

    public function __construct(Registry $doctrine)
    {
        $this->manager = $doctrine->getManager();
    }

    public function execute()
    {
        $entity = $this->getFromResponse('entity');

        try {
            $this->manager->persist($entity);
            $this->manager->flush();
        } catch (\Exception $exception) {
            throw new BadRequestHttpException('Your request can not be stored', $exception);
        }

        return $this->createResponse(['response_data' => $entity]);
    }

    public function requiresBefore()
    {
        return [ValidateFormStep::class];
    }


}