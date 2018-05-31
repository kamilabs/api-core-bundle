<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class HandleRequestStep extends AbstractStep
{
    public function execute(Request $request) : ArtifactCollection
    {
        /** @var Form $form */
        $form = $this->getArtifact('form');
        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            throw new BadRequestHttpException('Form is supposed to be submitted with this request');
        }

        return new ArtifactCollection([
            new Artifact('handled_request', true)
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['form', 'access_granted'];
    }

}