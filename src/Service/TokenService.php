<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 * @link     https://github.com/wayhood/hyperf-action
 */
namespace Wayhood\HyperfAction\Service;

use Wayhood\HyperfAction\Contract\TokenInterface;

/**
 * Class TokenService.
 */
class TokenService implements TokenInterface
{
    public function verify(string $token)
    {
        return true;
        // TODO: Implement verify() method.
    }

    public function has(string $token)
    {
        return '';
    }

    public function generator(array $value)
    {
        return '';
    }

    public function set(string $token)
    {
        return '';
    }

    public function get(string $token)
    {
        return '';
    }
}
