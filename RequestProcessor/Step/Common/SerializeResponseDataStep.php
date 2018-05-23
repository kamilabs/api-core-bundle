<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;

class SerializeResponseDataStep extends AbstractStep
{
    public function execute()
    {
        $serializer = $this->getFromResponse('serializer');

        $serialized = $serializer->serialize(
            $this->getFromResponse('response_data'),
            $this->request->attributes->get('_format')
        );
        return $this->createResponse(['response_data'=> $serialized], true);


    }

    public function requiresBefore()
    {
        return [BuildSerializerStep::class];
    }
}