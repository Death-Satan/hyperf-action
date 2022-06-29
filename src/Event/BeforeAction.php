<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 * @link     https://github.com/wayhood/hyperf-action
 */
namespace Wayhood\HyperfAction\Event;

class BeforeAction
{
    /**
     * Action Class.
     * @var string
     */
    protected $action;

    /**
     * 当前请求的参数.
     * @var array
     */
    protected $params = [];

    /**
     * 额外的参数.
     * @var array
     */
    protected $extras = [];

    /**
     * headers头部.
     * @var array
     */
    protected $headers = [];

    public function __construct(string $action, array &$params, array &$extras, array &$headers)
    {
        $this->action = $action;
        $this->params = $params;
        $this->extras = $extras;
        $this->headers = $headers;
    }
}
