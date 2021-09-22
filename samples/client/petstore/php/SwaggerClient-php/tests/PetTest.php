<?php
declare(strict_types=1);

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;
use Swagger\Client\Model\Pet;

class PetTest extends TestCase
{
    /**
     * test empty object serialization
     */
    public function testEmptyPetSerialization(): void
    {
        $new_pet = new Pet;
        // the empty object should be serialised to {}
        $this->assertSame("{}", (string)$new_pet);
    }
}
