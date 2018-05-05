<?php


namespace Kami\ApiCoreBundle\Annotation;

/**
 * @Annotation
 */
class Relation
{
    public $target;

    public $maxDepth = 1;
}