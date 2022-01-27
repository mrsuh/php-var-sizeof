<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class VarSizeOfTest extends TestCase
{
    public function testSizeOfInteger(): void
    {
        $a = 1;
        $this->assertEquals(16, var_sizeof($a));
    }

    public function testSizeOfDouble(): void
    {
        $a = 1.0;
        $this->assertEquals(16, var_sizeof($a));
    }

    public function testSizeOfObject(): void
    {
        $a = new \stdClass();
        $this->assertEquals(72, var_sizeof($a));
    }

    public function testSizeOfList0(): void
    {
        $a = [];
        $this->assertEquals(336, var_sizeof($a));
    }

    public function testSizeOfList1000(): void
    {
        $a = array_fill(0,1000,null);
        $this->assertEquals(16464, var_sizeof($a));
    }

    public function testSizeOfArray1000(): void
    {
        $a = array_fill(0,999,null);
        $a['index'] = null;
        $this->assertEquals(41032, var_sizeof($a));
    }
}
