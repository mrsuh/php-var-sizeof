<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class VarClassSizeOfTest extends TestCase
{
    public function testClassSizeOfObject(): void
    {
        $a = new \stdClass();
        $this->assertEquals(1360, var_class_sizeof($a));
    }
}
