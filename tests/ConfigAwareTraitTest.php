<?php

declare(strict_types=1);

namespace Phoole\Tests\Util;

use Phoole\Config\Config;
use PHPUnit\Framework\TestCase;
use Phoole\Config\ConfigAwareTrait;
use Phoole\Config\ConfigAwareInterface;

class NewClass implements ConfigAwareInterface
{
    use ConfigAwareTrait;
}

class ConfigAwareTraitTest extends TestCase
{
    private $obj;

    private $ref;

    private $config;

    protected function setUp(): void
    {
        parent::setUp();
        $dir = __DIR__ . \DIRECTORY_SEPARATOR . 'config';
        $this->config = new Config($dir);
        $this->obj = new NewClass();
    }

    protected function tearDown(): void
    {
        $this->obj = $this->ref = $this->config = NULL;
        parent::tearDown();
    }

    /**
     * @covers Phoole\Config\ConfigAwareTrait::setConfig()
     */
    public function testSetConfig()
    {
        $this->obj->setConfig($this->config);
        $this->assertTrue($this->config === $this->obj->getConfig());
    }

    /**
     * @covers Phoole\Config\ConfigAwareTrait::getConfig()
     */
    public function testGetConfig()
    {
        $this->expectExceptionMessage('Config not set in');
        $this->obj->getConfig();
    }
}