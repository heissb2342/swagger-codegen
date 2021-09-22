<?php
declare(strict_types=1);

namespace Swagger\Client;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Swagger\Client\Model\EnumTest;

class EnumTestTest extends TestCase
{
    public function testPossibleValues(): void
    {
        $this->assertSame(EnumTest::ENUM_STRING_UPPER, "UPPER");
        $this->assertSame(EnumTest::ENUM_STRING_LOWER, "lower");
        $this->assertSame(EnumTest::ENUM_INTEGER_1, 1);
        $this->assertSame(EnumTest::ENUM_INTEGER_MINUS_1, -1);
        $this->assertSame(EnumTest::ENUM_NUMBER_1_DOT_1, 1.1);
        $this->assertSame(EnumTest::ENUM_NUMBER_MINUS_1_DOT_2, -1.2);
    }

    public function testStrictValidation(): void
    {
        $enum = new EnumTest([
            'enum_string' => 0,
        ]);

        $this->assertFalse($enum->valid());

        $expected = [
            "invalid value for 'enum_string', must be one of 'UPPER', 'lower', ''",
            "'enum_string_required' can't be null",
        ];
        $this->assertSame($expected, $enum->listInvalidProperties());
    }

    public function testThrowExceptionWhenInvalidAmbiguousValueHasPassed(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $enum = new EnumTest();
        $enum->setEnumString(0);
    }

    public function testNonRequiredPropertyIsOptional(): void
    {
        $enum = new EnumTest([
            'enum_string_required' => 'UPPER',
        ]);
        $this->assertSame([], $enum->listInvalidProperties());
        $this->assertTrue($enum->valid());
    }

    public function testRequiredProperty(): void
    {
        $enum = new EnumTest();
        $this->assertSame(["'enum_string_required' can't be null"], $enum->listInvalidProperties());
        $this->assertFalse($enum->valid());
    }
}
