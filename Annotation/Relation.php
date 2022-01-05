<?php


namespace Kami\ApiCoreBundle\Annotation;

/**
 * @Annotation
 */
class Relation
{
    public string $target;

    public int $maxDepth = 1;
}