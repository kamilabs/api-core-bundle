<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Filter;


use Kami\ApiCoreBundle\Filter\Validator;
use Kami\ApiCoreBundle\RequestProcessor\ResponseInterface;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;

class ValidateFilters extends AbstractStep
{
    public function execute()
    {
        $validator = new Validator($this->request);

        return $this->createResponse(['filters' => $validator->getFilters()]);
    }

    public function requiresBefore()
    {
        return [];
    }

}