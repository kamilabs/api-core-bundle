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
            array_merge($this->response->getData(), $data),
            $isHttpReady,
            $status
        );
    }

    protected function getFromResponse($key)
    {
        return array_key_exists($key, $this->response->getData()) ? $this->response->getData()[$key] : null;
    }

    public function setPreviousResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getName()
    {
        return get_class($this);
    }
}