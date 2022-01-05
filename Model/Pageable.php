<?php

namespace Kami\ApiCoreBundle\Model;


class Pageable
{
    private iterable $content;
    private int $total;
    private $pageRequest;

    public function __construct(iterable $content, int $total, PageRequest $pageRequest)
    {
        $this->content = $content;
        $this->total = $total;
        $this->pageRequest = $pageRequest;
    }

    public function getContent() : iterable
    {
        return $this->content;
    }

    public function setContent(iterable $content) : void
    {
        $this->content = $content;
    }

    public function getTotal() : int
    {
        return $this->total;
    }

    public function setTotal($total) : void
    {
        $this->total = $total;
    }


    public function getPageRequest() : PageRequest
    {
        return $this->pageRequest;
    }

    public function setPageRequest(PageRequest $pageRequest)
    {
        $this->pageRequest = $pageRequest;
    }

}