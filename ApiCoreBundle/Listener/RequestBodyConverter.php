<?php

namespace Kami\ApiCoreBundle\Listener;

use JMS\Serializer\Serializer;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class RequestBodyConverter
 *
 * @package Kami\ApiCoreBundle\Listener
 */
class RequestBodyConverter
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (stristr($request->headers->get('content-type'), 'application/json')) {
            if ($request->getContent()) {
                try {
                    $data = json_decode($request->getContent(), true);
                    foreach ($data as $key => $value) {
                        $request->request->set($key, $value);
                    }
                } catch (\Exception $e) {
                };
            }
        }
    }
}
