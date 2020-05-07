<?php

declare(strict_types=1);
namespace Wayhood\HyperfAction\Annotation;


use Hyperf\Di\Annotation\AbstractAnnotation;
/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Action extends AbstractAnnotation
{
    /**
     * @var null|string
     */
    public $mapper = '';

    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->bindMainProperty('mapper', $value);
    }

}