<?php

declare(strict_types=1);

namespace Phoole\Tests\Util;

use Phoole\Config\Util\Loader;
use PHPUnit\Framework\TestCase;

class LoaderTest extends TestCase
{
    private $obj;

    private $ref;

    private $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = dirname(__DIR__) . \DIRECTORY_SEPARATOR . 'config';
        $this->obj = new Loader($this->dir);
        $this->ref = new \ReflectionClass(get_class($this->obj));
    }

    protected function tearDown(): void
    {
        $this->obj = $this->ref = NULL;
        parent::tearDown();
    }

    protected function invokeMethod($methodName, array $parameters = array())
    {
        $method = $this->ref->getMethod($methodName);
        $method->setAccessible(TRUE);
        return $method->invokeArgs($this->obj, $parameters);
    }

    /**
     * @covers Phoole\Config\Util\Loader::searchDirs()
     */
    public function testSearchDirs()
    {
        $this->assertEquals(
            $this->invokeMethod('searchDirs', ["dev"]),
            [$this->dir, $this->dir . \DIRECTORY_SEPARATOR . 'dev']
        );
    }

    /**
     * @covers Phoole\Config\Util\Loader::globFiles()
     */
    public function testGlobFiles()
    {
        $this->assertEquals(
            $this->invokeMethod('globFiles', ['db', 'dev']),
            [
                $this->dir . \DIRECTORY_SEPARATOR . 'db.php',
                $this->dir . \DIRECTORY_SEPARATOR . 'dev' . \DIRECTORY_SEPARATOR . 'db.php'
            ]
        );
    }

    /**
     * load specific group 'system'
     *
     * @covers Phoole\Config\Util\Loader::load()
     */
    public function testLoad1()
    {
        $this->obj->load('system');

        $this->assertEquals(
            $this->obj->getTree()->get('system.tmpdir'),
            '/tmp'
        );

        $this->assertEquals(
            $this->obj->getTree()->get('system.host.ip'),
            '192.168.1.120'
        );
    }

    /**
     * load specific group 'db' with env
     *
     * @covers Phoole\Config\Util\Loader::load()
     */
    public function testLoad2()
    {
        $this->obj->load('db');

        $this->assertEquals(
            $this->obj->getTree()->get('db.host.port'),
            3306
        );

        $this->obj->load('db', 'dev');
        $this->assertEquals(
            $this->obj->getTree()->get('db.host.port'),
            13306
        );
    }

    /**
     * load all
     *
     * @covers Phoole\Config\Util\Loader::load()
     */
    public function testLoad3()
    {
        $this->obj->load();

        $this->assertEquals(
            $this->obj->getTree()->get('db.host.port'),
            3306
        );

        $this->assertEquals(
            $this->obj->getTree()->get('system.tmpdir'),
            '/tmp'
        );
    }
}
