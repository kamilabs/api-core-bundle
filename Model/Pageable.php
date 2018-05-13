<?php

namespace Kami\ApiCoreBundle\Model;


class Pageable
{
    private $content;
    private $total;
    private $pageRequest;

    /**
     * Pageable constructor.
     * @param $content
     * @param $total int
     * @param $pageRequest PageRequest
     */
    public function __construct($content, $total, $pageRequest)
    {
        $this->content = $content;
        $this->total = $total;
        $this->pageRequest = $pageRequest;
    }

    /**
     * @return iterable
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param iterable $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param int $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return PageRequest
     */
    public function getPageRequest()
    {
        return $this->pageRequest;
    }

    /**
     * @param PageRequest $pageRequest
     */
    public function setPageRequest($pageRequest)
    {
        $this->pageRequest = $pageRequest;
    }


}