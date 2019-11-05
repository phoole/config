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
     * @param  ConfigInterface $config
     * @return $this
     */
    public function setConfig(ConfigInterface $config);

    /**
     * @return ConfigInterface
     * @throws \LogicException  if not set yet
     */
    public function getConfig(): ConfigInterface;
}