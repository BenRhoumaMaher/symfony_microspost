<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleUnitTest extends TestCase
{
    public function testSomething(): void
    {
        $this->assertTrue(true);
        $this->assertFalse(1 == '10');
    }
    public function testContains()
    {
        $this->assertContains(4, [1, 2, 3, 4]);
    }
    public function testCount()
    {
        $this->assertCount(1, ['foo']);
    }
    public function testEmpty()
    {
        $this->assertEmpty([]);
    }
    public function testEquals1()
    {
        $this->assertEquals(1, 1);
    }
    public function testEquals2()
    {
        $this->assertEquals('bar', 'bar');
    }
    public function testEquals3()
    {
        $this->assertEquals(['a', 'b', 'c'], ['a', 'b', 'c']);
    }
    public function testGreaterThan()
    {
        $this->assertGreaterThan(1, 2);
    }
    public function testLessThan()
    {
        $this->assertLessThan(2, 1);
    }
}
