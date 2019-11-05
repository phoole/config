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
        $this->assertEquals(3306, $this->obj->get('db.host.port'));
        $this->assertEquals('localhost', $this->obj->get('db.host.ip'));
        $this->assertEquals('/tmp', $this->obj->get('system.tmpdir'));
    }

    /**
     * test getenv
     *
     * @covers Phoole\Config\Config::get()
     * @covers Phoole\Config\Config::has()
     */
    public function testGet2()
    {
        $obj = new Config($this->dir, 'dev');
        $this->assertFalse($obj->has('ENV.TEST'));
        putenv('TEST=test');
        $this->assertTrue($obj->has('ENV.TEST'));
        $this->assertEquals('test', $obj->get('ENV.TEST'));
        putenv('TEST');
        $this->assertFalse($obj->has('ENV.TEST'));
        $this->assertFalse($obj->get('ENV.TEST'));
    }

    /**
     * @covers Phoole\Config\Config::with()
     */
    public function testWith()
    {
        $obj = $this->obj->with('redis', ['host.ip' => 'localhost']);

        // not same object
        $this->assertFalse($obj === $this->obj);
        $this->assertTrue($this->obj->get('redis.host') === NULL);

        // check values
        $this->assertEquals($obj->get('db.host.port'), 3306);
        $this->assertEquals($obj->get('redis.host'), ['ip' => 'localhost']);
    }
}
