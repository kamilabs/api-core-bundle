<?php


namespace Kami\ApiCoreBundle\RequestProcessor\Step\Update;



use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractBuildFormStep;
use Kami\Component\RequestProcessor\Artifact;
use Kami\Component\RequestProcessor\ArtifactCollection;
use Symfony\Component\HttpFoundation\Request;

class BuildUpdateFormStep extends AbstractBuildFormStep
{
    public function execute(Request $request) : ArtifactCollection
    {
        $builder = $this->getBaseFormBuilder($request->getMethod());
        /** @var \ReflectionClass $reflection */
        $reflection = $this->getArtifact('reflection');

        foreach ($reflection->getProperties() as $property) {
            if ($this->accessManager->canUpdateProperty($property)) {
                $this->addField($property, $builder);
            }
        }

        return new ArtifactCollection([
            new Artifact('form', $builder->getForm())
        ]);
    }

    public function getRequiredArtifacts() : array
    {
        return ['reflection', 'access_granted', 'entity'];
    }

}