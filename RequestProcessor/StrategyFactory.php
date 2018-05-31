<?php

namespace Kami\ApiCoreBundle\RequestProcessor;


use Doctrine\Common\Collections\ArrayCollection;
use Kami\Component\RequestProcessor\Step\StepInterface;
use Kami\Component\RequestProcessor\AbstractStrategy;

class StrategyFactory
{
    /**
     * @var ArrayCollection
     */
    protected $availableSteps;

    /**
     * StrategyFactory constructor.
     */
    public function __construct()
    {
        $this->availableSteps = new ArrayCollection();
    }

    /**
     * @param array $steps
     * @return AbstractStrategy
     */
    public function create(array $steps)
    {
        return new class($this->getStepObjects($steps)) extends AbstractStrategy {};
    }

    public function addStep(string $shortcut, StepInterface $step) : void
    {
        $this->availableSteps->set($shortcut, $step);
    }

    private function getStepObjects($steps) : array
    {

        $strategySteps = [];
        foreach ($steps as $step) {
            $strategySteps[] = $this->availableSteps->get($step);
        }

        return $strategySteps;
    }

}