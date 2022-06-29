<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 * @link     https://github.com/wayhood/hyperf-action
 */
namespace Wayhood\HyperfAction\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use Wayhood\HyperfAction\Collector\ActionCollector;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class Action extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $mapping = '';

    public function __construct($value)
    {
        parent::__construct($value);
        $this->bindMainProperty('mapping', $value);
    }

    public function collectClass(string $className): void
    {
        ActionCollector::collectClass($className, static::class, $this->mapping);
    }
}
