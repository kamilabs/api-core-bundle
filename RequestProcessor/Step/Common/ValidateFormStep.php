<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Kami\Component\RequestProcessor\Step\AbstractStep;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;


class ValidateFormStep extends AbstractStep
{
    public function execute(Request $request) : ArtifactCollection
    {
        /** @var Form $form */
        $form = $this->getArtifact('form');
        if (!$form->isValid()) {
            return new ArtifactCollection([
                new Artifact('validation', false),
                new Artifact('status', 400),
                new Artifact('response_data', $form)
            ]);
        }

        return new ArtifactCollection([
            new Artifact('validation', true)
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['handled_request', 'access_granted', 'form'];
    }

}