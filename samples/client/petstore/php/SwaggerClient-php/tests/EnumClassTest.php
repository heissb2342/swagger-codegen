<?php
declare(strict_types=1);

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;
use Swagger\Client\Model\EnumClass;

class EnumClassTest extends TestCase
{
    public function testPossibleValues(): void
    {
        $this->assertSame(EnumClass::ABC, '_abc');
        $this->assertSame(EnumClass::EFG, '-efg');
        $this->assertSame(EnumClass::XYZ, '(xyz)');
    }
}
