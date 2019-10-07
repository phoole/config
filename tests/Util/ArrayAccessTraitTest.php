<?php

declare(strict_types=1);

namespace Phoole\Tests\Util;

use Phoole\Config\Util\Loader;
use PHPUnit\Framework\TestCase;

class ArrayAccess implements \ArrayAccess
{
    use \Phoole\Config\Util\ArrayAccessTrait;

    protected $data = [ 'a' => 'a'];

    public function has(string $name)
    {
        return isset($this->data[$name]);
    }

    public function get(string $name)
    {
        return $this->data[$name] ?? null;
    }
}

class ArrayAccessTraitTest extends TestCase
{
    private $obj;
    private $ref;

    protected function setUp(): void
    {
        parent::setUp();
        $this->obj = new ArrayAccess;
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
     * @covers Phoole\Config\Util\ArrayAccessTrait::offsetExists()
     */
    public function testOffsetExists()
    {
        $o = $this->obj;

        // found
        $this->assertTrue(isset($o['a']));

        // not found
        $this->assertFalse(isset($o['b']));
    }

    /**
     * @covers Phoole\Config\Util\ArrayAccessTrait::offsetGet()
     */
    public function testOffsetGet()
    {
        $o = $this->obj;

        // found
        $this->assertEquals('a', $o['a']);

        // not found
        $this->assertEquals(null, $o['b']);
    }

    /**
     * @covers Phoole\Config\Util\ArrayAccessTrait::offsetSet()
     */
    public function testOffsetSet()
    {
        $o = $this->obj;

        $this->expectExceptionMessage('config is immutable');
        $o['b'] = 'b';
    }

    /**
     * @covers Phoole\Config\Util\ArrayAccessTrait::offsetUnset()
     */
    public function testOffsetUnset()
    {
        $o = $this->obj;

        $this->expectExceptionMessage('config is immutable');
        unset($o['a']);
    }
}
