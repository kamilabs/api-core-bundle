<?php

namespace Kami\ApiCoreBundle\RequestProcessor;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ProcessorResponse implements ResponseInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var int
     */
    protected $status;

    /**
     * @var bool
     */
    protected $isHttpReady = false;

    public function __construct(Request $request, array $data, $httpReady = false, $status = 200)
    {
        $this->request = $request;
        $this->data = $data;
        $this->isHttpReady = $httpReady;
        $this->status = $status;
    }

    public function toHttpResponse()
    {
        if (!$this->isHttpReady) {
            throw new ProcessingException('Response is not ready yet to be set as http');
        }
        return $this->createResponse($this->request);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    private function createResponse(Request $request)
    {
        $format = $request->attributes->get('_format');

        return new Response(
            $this->data['response_data'],
            $this->status,
            ['Content-type' => $this->getContentTypeByFormat($format)]
        );
    }

    /**
     * @param string $format
     * @return string
     */
    private function getContentTypeByFormat($format)
    {
        switch ($format) {
            case 'json':
                return 'application/json';
            case 'xml':
                return 'application/xml';
            default:
                return 'text/plain';
        }
    }
}