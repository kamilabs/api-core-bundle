<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ValidateFormStep extends AbstractStep
{
    public function execute()
    {
        /** @var Form $form */
        $form = $this->getFromResponse('form');
        if (!$form->isValid()) {
            return $this->createResponse(['response_data' => $form->getErrors()], true, 400);
        }

        return $this->createResponse([]);
    }

    public function requiresBefore()
    {
        return [HandleRequestStep::class];
    }

}