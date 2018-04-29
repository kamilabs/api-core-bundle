<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step\Common;

use JMS\Serializer\Serializer;
use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\Step\AbstractStep;

class SerializeResponseDataStep extends AbstractStep
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function execute()
    {
        $serialized = $this->serializer->serialize(
            $this->getFromResponse('response_data'),
            $this->request->attributes->get('_format')
        );

        return new ProcessorResponse(
            $this->request,
            array_merge(['response_data' => $serialized],
                $this->response->getData()
            ),
            true,
            200
        );
    }

    public function setSerializer(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function requiresBefore()
    {
        return [];
    }
}