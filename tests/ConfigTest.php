<?php

declare(strict_types=1);

namespace Phoole\Tests\Util;

use Phoole\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    private $obj;
    private $ref;
    private $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = __DIR__ . \DIRECTORY_SEPARATOR . 'config';
        $this->obj = new Config($this->dir);
        $this->ref = new \ReflectionClass(get_class($this->obj));
    }

    protected function tearDown(): void
    {
        $this->obj = $this->ref = null;
        parent::tearDown();
    }

    protected function invokeMethod($methodName, array $parameters = array())
    {
        $method = $this->ref->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($this->obj, $parameters);
    }

    /**
     * @covers Phoole\Config\Config::__construct()
     */
    public function testConfig()
    {
        $c = [
            'db.user' => 'root'
        ];

        # load conf from array
        $obj = new Config($c);
        $this->assertEquals(['db' => ['user' => 'root']], $obj->get(''));
        $this->assertEquals('root', $obj->get('db.user'));
    }

    /**
     * @covers Phoole\Config\Config::has()
     */
    public function testHas()
    {
        $this->assertTrue($this->obj->has('db'));
        $this->assertTrue($this->obj->has('system.tmpdir'));
    }

    /**
     * @covers Phoole\Config\Config::get()
     */
    public function testGet1()
    {
        $this->assertEquals($this->obj->get('db.host.port'), 3306);
        $this->assertEquals($this->obj->get('db.host.ip'), 'localhost');
        $this->assertEquals($this->obj->get('system.tmpdir'), '/tmp');
    }

    /**
     * @covers Phoole\Config\Config::get()
     */
    public function testGet2()
    {
        $obj = new Config($this->dir, 'dev');
        $this->assertEquals($obj->get('db.host.port'), 13306);
        $this->assertEquals($obj->get('db.host.ip'), '192.168.1.120');
        $this->assertEquals($obj->get('system.tmpdir'), '/tmp');
    }

    /**
     * @covers Phoole\Config\Config::with()
     */
    public function testWith()
    {
        $obj = $this->obj->with('redis', ['host.ip' => 'localhost']);

        // not same object
        $this->assertFalse($obj === $this->obj);
        $this->assertTrue($this->obj->get('redis.host') === null);

        // check values
        $this->assertEquals($obj->get('db.host.port'), 3306);
        $this->assertEquals($obj->get('redis.host'), ['ip' => 'localhost']);
    }
}
