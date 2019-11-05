<?php

/**
 * Phoole (PHP7.2+)
 *
 * @category  Library
 * @package   Phoole\Config
 * @copyright Copyright (c) 2019 Hong Zhang
 */
declare(strict_types = 1);

namespace Phoole\Config;

/**
 * ConfigInterface
 *
 * @package Phoole\Config
 */
interface ConfigInterface
{
    /**
     * Get a configure value. NUll return if not found
     *
     * If you want a default value,
     * ```php
     * $user = $conf->get('user) ?? 'dummy';
     * ```
     *
     * @param  string $id
     * @return mixed
     */
    public function get(string $id);

    /**
     * Has a configure key
     *
     * @param  string $id
     * @return bool
     */
    public function has(string $id): bool;

    /**
     * Config object is supposed to be IMMUTABLE once created.
     *
     * Set new value will return a new copy
     * ```php
     * $newconf = $conf->with('db.user', 'system');
     * ```
     *
     * @param  string $id
     * @param  mixed  $value
     * @return ConfigInterface
     */
    public function with(string $id, $value): ConfigInterface;
}