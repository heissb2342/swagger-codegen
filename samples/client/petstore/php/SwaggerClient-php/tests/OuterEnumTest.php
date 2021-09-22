<?php
declare(strict_types=1);

namespace Swagger\Client;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Swagger\Client\Model\EnumTest;
use Swagger\Client\Model\OuterEnum;

class OuterEnumTest extends TestCase
{
    public function testDeserialize(): void
    {
        $result = ObjectSerializer::deserialize(
            "placed",
            OuterEnum::class
        );

        $this->assertIsString($result);
        $this->assertEquals('placed', $result);
    }

    public function testDeserializeInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for enum');

        ObjectSerializer::deserialize(
            "lkjfalgkdfjg",
            OuterEnum::class
        );
    }

    public function testDeserializeNested(): void
    {
        $json = '{
            "enum_string": "UPPER",
            "enum_integer": -1,
            "enum_number": -1.2, 
            "outerEnum": "approved"
        }';

        /** * @var EnumTest $result */
        $result = ObjectSerializer::deserialize(
            json_decode($json, false, 512, JSON_THROW_ON_ERROR),
            EnumTest::class
        );

        $this->assertInstanceOf(EnumTest::class, $result);
        $this->assertEquals('approved', $result->getOuterEnum());
    }

    public function testSanitize(): void
    {
        $json = "placed";

        $result = ObjectSerializer::sanitizeForSerialization(
            $json
        );

        $this->assertIsString($result);
    }

    public function testSanitizeNested(): void
    {
        $input = new EnumTest([
            'enum_string' => 'UPPER',
            'enum_integer' => -1,
            'enum_number' => -1.2,
            'outer_enum' => 'approved'
        ]);

        $result = ObjectSerializer::sanitizeForSerialization(
            $input
        );

        $this->assertIsObject($result);
        $this->assertInstanceOf(\stdClass::class, $result);

        $this->assertIsString($result->outerEnum);
        $this->assertEquals('approved', $result->outerEnum);
    }

    public function testSanitizeNestedInvalidValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid value for enum');

        $input = new EnumTest([
            'enum_string' => 'UPPER',
            'enum_integer' => -1,
            'enum_number' => -1.2,
            'outer_enum' => 'invalid_value'
        ]);

        ObjectSerializer::sanitizeForSerialization($input);
    }
}
