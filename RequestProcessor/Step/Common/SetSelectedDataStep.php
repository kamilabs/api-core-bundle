<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Pagerfanta\Pagerfanta;

class SetSelectedDataStep extends AbstractStep
{
    public function execute()
    {
        /** @var Pagerfanta $paginator */
        $paginator = $this->getFromResponse('paginator');
        $responseData = [
            'rows'         => $paginator->getCurrentPageResults(),
            'total'        => $paginator->getNbResults(),
            'current_page' => $paginator->getCurrentPage(),
            'total_pages'  => $paginator->getNbPages()
        ];

        $this->createResponse(['response_data' => $responseData]);
    }

    public function requiresBefore()
    {
        return [PaginateStep::class];
    }

}