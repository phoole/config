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

use Phoole\Base\Tree\Tree;
use Phoole\Base\Reader\Reader;

/**
 * Config file loader
 *
 * @package Phoole\Config
 */
class Loader
{
    /**
     * tree object
     */
    protected $tree;

    /**
     * reader object
     */
    protected $reader;

    /**
     * conf directory
     */
    protected $root_dir;

    /**
     * default environment
     */
    protected $environment;

    /**
     * Constructor
     *
     * @param  string $rootDir
     * @param  string $environment
     */
    public function __construct(string $rootDir, string $environment = '')
    {
        $this->tree = new Tree();
        $this->reader = new Reader();
        $this->environment = $environment;
        $this->root_dir = rtrim($rootDir, "/\\");
    }

    /**
     * Load group(specific) config(s) base on environment value.
     *
     * - if $environment == '', use the default environment
     * - if $group == '', load all avaiable groups
     *
     * ```php
     * $this->load('db')->load('db', 'production/host1');
     * ```
     *
     * @param  string $group
     * @param  string $environment
     * @return $this
     */
    public function load(string $group = '', string $environment = ''): object
    {
        foreach ($this->globFiles($group, $environment) as $file) {
            $grp  = explode('.', basename($file), 2)[0];
            $this->tree->add($grp, $this->reader->readFile($file));
        }
        return $this;
    }

    /**
     * @return Tree
     */
    public function getTree(): Tree
    {
        return $this->tree;
    }

    /**
     * Returns an array of conf files to read from
     *
     * @param  string $group
     * @param  string $environment
     * @return array
     */
    protected function globFiles(string $group, string $environment): array
    {
        $files = [];
        $grp = ($group ?: '*') . '.*';
        foreach ($this->searchDirs($environment) as $dir) {
            $globs = \glob($dir . \DIRECTORY_SEPARATOR . $grp);
            $files = array_merge($files, $globs ?: []);
        }
        return $files;
    }

    /**
      * Returns an array of directoris to search thru
      *
      * @param  string $environment
      * @return array
      */
    protected function searchDirs(string $environment): array
    {
        $envs  = preg_split(
            '~/~',
            trim($environment ?: $this->environment, '/'),
            -1,
            \PREG_SPLIT_NO_EMPTY
        );

        $d = $this->root_dir;
        $dirs = [ $d ];
        foreach ($envs as $p) {
            $d .= \DIRECTORY_SEPARATOR . $p;
            $dirs[] = $d;
        }
        return $dirs;
    }
}
