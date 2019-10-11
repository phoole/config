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

use Phoole\Base\Tree\Tree;
use Phoole\Config\Util\Loader;
use Phoole\Base\Reference\ReferenceTrait;
use Phoole\Base\Reference\ReferenceInterface;

/**
 * Config
 *
 * @package Phoole\Config
 */
class Config implements ConfigInterface, ReferenceInterface
{
    use ReferenceTrait;

    /**
     * @var    \Phoole\Base\Tree\Tree
     */
    protected $tree;

    /**
     * Constructor
     *
     * ```php
     * # load from files
     * $conf = new Config('/my/app/conf', 'product/host1');
     *
     * # load from array
     * $conf = new Config(['db.user'=> 'root']);
     * ```
     *
     * @param  string|array $dirOrConfData
     * @param  string $environment
     */
    public function __construct($dirOrConfData, string $environment = '')
    {
        if (is_string($dirOrConfData)) {
            $this->tree = (new Loader($dirOrConfData, $environment))->load()->getTree();
        } else {
            $this->tree = new Tree($dirOrConfData);
        }

        // do dereferencing
        $conf = &$this->tree->get('');
        $this->deReference($conf);
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $id)
    {
        try {
            return $this->tree->get($id);
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        return $this->tree->has($id);
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
     * Get the tree object
     * @return Tree
     */
    public function getTree(): Tree
    {
        return $this->tree;
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
        $this->tree = clone $this->tree;
    }
}
