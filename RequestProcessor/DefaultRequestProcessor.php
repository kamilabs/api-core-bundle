<?php

namespace Kami\ApiCoreBundle\RequestProcessor;


use Kami\ApiCoreBundle\RequestProcessing\Strategy\RequestProcessorInterface;
use Kami\ApiCoreBundle\RequestProcessor\Step\StepInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultRequestProcessor implements RequestProcessorInterface
{
    /**
     * @var array
     */
    protected $indexStrategy;

    /**
     * @var array
     */
    protected $singleStrategy;

    /**
     * @var array
     */
    protected $filterStrategy;

    /**
     * @var array
     */
    protected $createStrategy;

    /**
     * @var array
     */
    protected $updateStrategy;

    /**
     * @var array
     */
    protected $deleteStrategy;

    /**
     * @var array
     */
    protected $myStrategy;

    /**
     * @param Request $request
     * @return ProcessorResponse|ResponseInterface|\Symfony\Component\HttpFoundation\Response
     */
    public function getIndex(Request $request)
    {
        return $this->executeStrategy($this->indexStrategy, $request);
    }

    public function getSingle(Request $request)
    {
        return $this->executeStrategy($this->singleStrategy, $request);
    }

    public function filter(Request $request)
    {
        return $this->executeStrategy($this->filterStrategy, $request);
    }

    public function create(Request $request)
    {
        return $this->executeStrategy($this->createStrategy, $request);
    }

    public function update(Request $request)
    {
        return $this->executeStrategy($this->updateStrategy, $request);
    }

    public function delete(Request $request)
    {
        return $this->executeStrategy($this->deleteStrategy, $request);
    }

    public function my(Request $request)
    {
        return $this->executeStrategy($this->myStrategy, $request);
    }

    protected function executeStrategy(array $strategy, Request $request)
    {
        $response = new ProcessorResponse($request, []);
        $executedSteps = [];

        foreach ($strategy as $step) { /** @var StepInterface $step */
            if (!in_array($step->requiresBefore(), $executedSteps)) {
                throw new ProcessingException(
                    "Request didn't pass required steps yet. Try to adjust your processing strategy\n" .
                    "Required steps are: " . implode(',',  $step->requiresBefore())
                );

            }

            $response = $step->execute();

            if(!$response) {
                throw new ProcessingException(
                    sprintf('RequestProcessor didn\'t receive any response from %s'), get_class($step));
            }
            $executedSteps[] = $step->getName();

            if (200 !== $response->getStatus()) {
                break;
            }
        }

        return $response->toHttpResponse();
    }


}