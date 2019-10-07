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

    public function offsetSet(
        /** @scrutinizer ignore-unused */ $offset,
        /** @scrutinizer ignore-unused */ $value
    ): void {
        throw new \RuntimeException("config is immutable");
    }

    public function offsetUnset(/** @scrutinizer ignore-unused */ $offset): void
    {
        throw new \RuntimeException("config is immutable");
    }

    // from ConfigInterface
    abstract public function has(string $id): bool;
    abstract public function get(string $id);
}
