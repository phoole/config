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
 * ConfigAwareTrait
 *
 * @package Phoole\Config
 */
trait ConfigAwareTrait
{
    /**
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @param  ConfigInterface $config
     * @return $this
     */
    public function setConfig(ConfigInterface $config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * @return ConfigInterface
     * @throws \LogicException  if not set yet
     */
    public function getConfig(): ConfigInterface
    {
        if (is_null($this->config)) {
            throw new \LogicException("Config not set in " . get_class($this));
        }
        return $this->config;
    }
}