<?php


namespace Kami\ApiCoreBundle\Filter;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Validator
{
    protected  $filters = [];

    const ALLOWED_FILTERS = ['eq', 'gt', 'lt', 'in', 'bw', 'lk'];

    public function __construct(Request $request)
    {
        if ($filter = $request->get('filter')) {
            $filters = json_decode(base64_decode($filter), true);
            if($filters) {
                $this->filters = $filters;
                $this->validate();
            }
        }
    }

    public function getFilters()
    {
        return $this->filters;
    }

    protected function validate()
    {
        foreach ($this->filters as $filter) {
            if (!in_array($filter['type'], self::ALLOWED_FILTERS)) {
                throw new BadRequestHttpException(sprintf('Filter %s is not allowed', $filter['type']));
            }
            call_user_func([$this, sprintf('validate%sFilter', ucfirst($filter['type']))], $filter);
        }
    }

    protected function validateEqFilter($filter)
    {
        $required = ['property', 'value'];
        $this->validateRequired($required, $filter);
    }

    protected function validateGtFilter($filter)
    {
        $required = ['property', 'value'];
        $this->validateRequired($required, $filter);
    }

    protected function validateLtFilter($filter)
    {
        $required = ['property', 'value'];
        $this->validateRequired($required, $filter);
    }

    protected function validateInFilter($filter)
    {
        $required = ['property', 'value'];
        $this->validateRequired($required, $filter);
    }

    protected function validateBwFilter($filter)
    {
        $required = ['property', 'min', 'max'];
        $this->validateRequired($required, $filter);
    }

    protected function validateLkFilter($filter)
    {
        $required = ['property', 'value'];
        $this->validateRequired($required, $filter);
    }

    protected function validateRequired($required, $filter)
    {
        foreach ($required as $param) {
            if (!array_key_exists($param, $filter)) {
               throw new BadRequestHttpException(sprintf(
                   'Filter doesn\'t contain required params. Required params are %s',
                   implode(', ', $required)
               ));
            }
        }
    }
}