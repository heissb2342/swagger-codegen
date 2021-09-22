<?php
declare(strict_types=1);

namespace Swagger\Client;

// test object serializer
use PHPUnit\Framework\TestCase;

class ObjectSerializerTest extends TestCase
{
    /**
     * Test the sanitizeForSerialization method with a stdClass.
     */
    public function testSanitizeForSerializationWithStdClass(): void
    {
        // Build a stdClass object.
        $obj = new \stdClass();
        $obj->prop1 = 'val1';
        $obj->prop2 = 'val2';

        // Call the method.
        $serialized = ObjectSerializer::sanitizeForSerialization($obj);

        // Assert that the stdClass object is sanitized as expected.
        $this->assertEquals('val1', $serialized->prop1);
        $this->assertEquals('val2', $serialized->prop2);
    }
    
    // test sanitizeFilename
    public function testSanitizeFilename(): void
    {
        $this->assertSame("sun.gif", ObjectSerializer::sanitizeFilename("sun.gif"));
        $this->assertSame("sun.gif", ObjectSerializer::sanitizeFilename("../sun.gif"));
        $this->assertSame("sun.gif", ObjectSerializer::sanitizeFilename("/var/tmp/sun.gif"));
        $this->assertSame("sun.gif", ObjectSerializer::sanitizeFilename("./sun.gif"));

        $this->assertSame("sun", ObjectSerializer::sanitizeFilename("sun"));
        $this->assertSame("sun.gif", ObjectSerializer::sanitizeFilename("..\sun.gif"));
        $this->assertSame("sun.gif", ObjectSerializer::sanitizeFilename("\var\tmp\sun.gif"));
        $this->assertSame("sun.gif", ObjectSerializer::sanitizeFilename("c:\var\tmp\sun.gif"));
        $this->assertSame("sun.gif", ObjectSerializer::sanitizeFilename(".\sun.gif"));
    }
}
