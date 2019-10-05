<?php

/**
 * Phoole (PHP7.2+)
 *
 * @category  Library
 * @package   Phoole\Config
 * @copyright Copyright (c) 2019 Hong Zhang
 */
declare(strict_types=1);

namespace Phoole\Config\Loader;

/**
 * LoaderInterface
 *
 * @package Phoole\Config
 */
interface LoaderInterface
{
    /**
     * Load group(specific) config(s) base on environment value.
     *
     * - if $environment == '', use the default environment
     * - if $group == '', load all avaiable groups
     *
     * ```php
     * $this->load('db');
     * $this->load('db', 'production/host1');
     * ```
     *
     * @param  string $group
     * @param  string $environment
     * @return array
     */
    public function load(string $group, string $environment = ''): array;
}
