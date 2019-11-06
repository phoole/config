<?php

/**
 * Phoole (PHP7.2+)
 *
 * @category  Library
 * @package   Phoole\Config
 * @copyright Copyright (c) 2019 Hong Zhang
 */
declare(strict_types=1);

namespace Phoole\Config;

/**
 * ConfigAwareInterface
 *
 * @package Phoole\Config
 */
interface ConfigAwareInterface
{
    /**
     * @param  Config $config
     * @return $this
     */
    public function setConfig(Config $config);

    /**
     * @return Config
     * @throws \LogicException  if not set yet
     */
    public function getConfig(): Config;
}