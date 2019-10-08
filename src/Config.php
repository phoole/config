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

use Phoole\Config\Util\{Loader, ArrayAccessTrait};
use Phoole\Base\Reference\{ReferenceInterface, ReferenceTrait};

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
     * @var    \Phoole\Base\Tree\Tree
     */
    protected $tree;

    /**
     * Constructor
     *
     * @param  string $rootDir
     * @param  string $environment
     */
    public function __construct(string $rootDir, string $environment = '')
    {
        // load all config files
        $this->tree = (new Loader($rootDir, $environment))->load()->getTree();

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
