<?php

namespace Kami\ApiCoreBundle\RequestProcessor;

use Doctrine\Common\Collections\ArrayCollection;
use Kami\Component\RequestProcessor\RequestProcessorInterface;

final class ProcessorRegistry 
{
    private $processors;

    public function __construct()
    {
        $this->processors = new ArrayCollection();
    }

    public function addProcessor(string $name, RequestProcessorInterface $requestProcessor) : ProcessorRegistry
    {
        $this->processors->set($name, $requestProcessor);

        return $this;
    }

    public function getProcessor(string $name): ?RequestProcessorInterface
    {
        return $this->processors->get($name);
    }
}