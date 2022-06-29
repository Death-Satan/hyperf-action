<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 * @link     https://github.com/wayhood/hyperf-action
 */
namespace Wayhood\HyperfAction\Contract;

interface SignInterface
{
    public function verify(string $secret, array $request, string $sign);
}
