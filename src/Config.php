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

use Phoole\Config\Util\Loader;
use Phoole\Config\Util\ArrayAccessTrait;
use Phoole\Base\Reference\ReferenceInterface;
use Phoole\Base\Reference\ReferenceTrait;

/**
 * Config
 *
 * @package Phoole\Config
 */
class Config implements ConfigInterface, ReferenceInterface, \ArrayAccess
{
    use ReferenceTrait;
    use ArrayAccessTrait;

    /**
     * @var    Loader
     */
    protected $loader;

    /**
     * @var    Phoole\Base\Tree\Tree
     */
    protected $tree;

    /**
     * @var    string
     */
    private $cached_id;

    /**
     * @var    mixed
     */
    private $cached_value;

    /**
     * Constructor
     *
     * @param  string $rootDir
     * @param  string $environment
     */
    public function __construct(string $rootDir, string $environment = '')
    {
        $this->loader = (new Loader($rootDir, $environment))->load();
        $this->tree = $this->loader->getTree();
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id)
    {
        if ($this->has($id)) {
            $val = $this->cached_value;
            $this->deReference($val);
            return $val;
        }
        return null;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        if ($id === $this->cached_id) {
            return null !== $this->cached_value;
        }

        $this->cached_id = $id;
        $this->cached_value = null;

        try {
            $this->cached_value = $this->tree->get($id);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
        return null !== $this->cached_value;
    }

    /**
     * {@inheritDoc}
     */
    public function with(string $id, $value): ConfigInterface
    {
        $new = clone $this;
        $new->tree->add($id, $value);
        return $new;
    }

    /**
     * {@inheritDoc}
     */
    protected function getReference(string $name)
    {
        return $this->get($name);
    }

    public function __clone()
    {
        $this->loader = clone $this->loader;
        $this->tree = $this->loader->getTree();
    }
}
