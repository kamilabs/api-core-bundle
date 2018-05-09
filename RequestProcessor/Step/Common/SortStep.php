<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Doctrine\ORM\QueryBuilder;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Kami\ApiCoreBundle\Security\AccessManager;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SortStep extends AbstractStep
{
    protected $accessManager;

    public function __construct(AccessManager $accessManager)
    {
        $this->accessManager = $accessManager;
    }

    public function execute()
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->getFromResponse('query_builder');
        $sort = $this->request->get('sort', $this->request->attributes->get('_sort'));
        $direction = $this->request->get('direction', $this->request->attributes->get('_sort_direction'));
        if (!in_array($direction, ['asc', 'desc'])) {
            throw new BadRequestHttpException();
        }
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getFromResponse('reflection');
        $property = $reflection->getProperty($sort);

        if ($sort !== $this->request->attributes->get('_sort')
            && !$this->accessManager->canAccessProperty($property)) {
            throw new AccessDeniedHttpException();
        }

        $queryBuilder->orderBy(sprintf('e.%s', $sort), $direction);

        return $this->createResponse(['query_builder' => $queryBuilder]);
    }

    public function requiresBefore()
    {
        return [GetQueryBuilderStep::class];
    }

}