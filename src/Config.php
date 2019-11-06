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
use Phoole\Base\Tree\TreeAwareTrait;
use Phoole\Base\Tree\TreeAwareInterface;
use Phoole\Base\Reference\ReferenceTrait;
use Phoole\Base\Reference\ReferenceInterface;

/**
 * Config
 *
 * @package Phoole\Config
 */
class Config implements ConfigInterface, ReferenceInterface, TreeAwareInterface
{
    use ReferenceTrait;
    use TreeAwareTrait;

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
     * @param  string       $environment
     */
    public function __construct(
        $dirOrConfData,
        string $environment = ''
    ) {
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
            if (0 === strpos($id, 'ENV.')) {
                return getenv(substr($id, 4));
            } else {
                return $this->tree->get($id);
            }
        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $id): bool
    {
        if (0 === strpos($id, 'ENV.')) {
            return FALSE !== getenv(substr($id, 4));
        } else {
            return $this->tree->has($id);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getReference(string $name)
    {
        return $this->get($name);
    }
}