<?php

namespace Kami\ApiCoreBundle\RequestProcessor\Step;


use Kami\ApiCoreBundle\RequestProcessor\ProcessorResponse;
use Kami\ApiCoreBundle\RequestProcessor\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractStep implements StepInterface
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    public function __construct(Request $request, ResponseInterface $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @param array $data
     * @param bool $isHttpReady
     * @param int $status
     *
     * @return ProcessorResponse
     */
    protected function createResponse(array $data, $isHttpReady = false, $status = 200)
    {
        return new ProcessorResponse(
            $this->request,
            array_merge($data, $this->response->getData()),
            $isHttpReady,
            $status
        );
    }

    protected function getFromResponse($key)
    {
        return array_key_exists($key, $this->response->getData()) ? $this->response->getData()[$key] : null;
    }


    public function getName()
    {
        return self::class;
    }
}