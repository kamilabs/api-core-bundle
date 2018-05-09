<?php

namespace Kami\ApiCoreBundle\RequestProcessor;


use Kami\ApiCoreBundle\RequestProcessor\Step\StepInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultRequestProcessor implements RequestProcessorInterface
{
    protected $availableSteps;

    protected $executedSteps;

    public function addStep($shortcut, StepInterface $step)
    {
        $this->availableSteps[$shortcut] = $step;
    }

    public function executeStrategy(array $strategy, Request $request)
    {
        $response = new ProcessorResponse($request, []);
        $this->executedSteps = [];

        foreach ($strategy as $shortcut) {
            $step = $this->availableSteps[$shortcut];

            $response = $this->executeStep($step, $request, $response);
            if (200 !== $response->getStatus()) {
                break;
            }
        }

        return $response->toHttpResponse();
    }

    protected function executeStep(StepInterface $step, $request, $response)
    {
        $this->checkHasPassedRequiredSteps($step);
        $step->setRequest($request);
        $step->setPreviousResponse($response);
        $response = $step->execute();
        $this->executedSteps[] = $step->getName();

        return $response;
    }

    protected function checkHasPassedRequiredSteps(StepInterface $step)
    {
        if (0 === count($step->requiresBefore())) {
            return;
        }
        foreach ($step->requiresBefore() as $requiredStep) {
            if (!in_array($requiredStep, $this->executedSteps)) {
                throw new ProcessingException(sprintf(
                    "Request didn't pass required steps yet. Try to adjust your processing strategy\n".
                    "Required steps for %s are: %s", $step->getName(), implode(',', $step->requiresBefore())
                ));
            }
        }
    }
}