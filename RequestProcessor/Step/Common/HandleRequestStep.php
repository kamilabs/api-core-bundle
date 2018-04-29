<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;


use Kami\ApiCoreBundle\RequestProcessor\ResponseInterface;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HandleRequestStep extends AbstractStep
{
    public function execute()
    {
        /** @var Form $form */
        $form = $this->getFromResponse('form');
        $form->handleRequest($this->request);

        if (!$form->isSubmitted()) {
            throw new BadRequestHttpException('Form is supposed to be submitted with this request');
        }

        return $this->createResponse(['form' => $form]);
    }

    public function requiresBefore()
    {
        return ['generic_build_form_step'];
    }

}