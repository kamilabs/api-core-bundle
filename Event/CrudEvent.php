<?php

namespace Kami\ApiCoreBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class CrudEvent extends Event
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * CrudEvent constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
