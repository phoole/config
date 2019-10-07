<?php

/**
 * Phoole (PHP7.2+)
 *
 * @category  Library
 * @package   Phoole\Config
 * @copyright Copyright (c) 2019 Hong Zhang
 */
declare(strict_types=1);

namespace Phoole\Config\Util;

/**
 * Implementation of \ArrayAccess
 *
 * @package Phoole\Config
 * @see     \ArrayAccess
 */
trait ArrayAccessTrait
{
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value): void
    {
        throw new \RuntimeException("config is immutable");
    }

    public function offsetUnset($offset): void
    {
        throw new \RuntimeException("config is immutable");
    }
}
