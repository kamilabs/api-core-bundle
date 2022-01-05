<?php

namespace Kami\ApiCoreBundle\Model;


class PageRequest
{
    private int $page;
    
    private int $size;

    public function __construct(int $page, int $size)
    {
        $this->page = $page;
        $this->size = $size;
    }

    public function getPage() : int
    {
        return $this->page;
    }

    public function setPage($page) : void
    {
        $this->page = $page;
    }

    public function getSize() : int
    {
        return $this->size;
    }

    public function setSize(int $size) : void
    {
        $this->size = $size;
    }
}