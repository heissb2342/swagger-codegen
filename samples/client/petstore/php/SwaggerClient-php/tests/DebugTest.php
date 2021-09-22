<?php
namespace Swagger\Client;

use PHPUnit\Framework\TestCase;

class DebugTest extends TestCase
{
    private const PET_ID = 10005;

    private Api\PetApi $api;

    protected function setUp(): void
    {
        $newPet = $this->preparePet();

        $config = new Configuration();
        $config->setDebug(true);
        
        $this->api = new Api\PetApi(null, $config);
        $this->api->addPet($newPet);
    }

    public function testEnableDebugOutput(): void
    {
        $this->expectOutputRegex('#GET /v2/pet/' . self::PET_ID . ' HTTP/1.1#');

        $this->api->getPetById(self::PET_ID);
    }

    public function testEnableDebugOutputAsync(): void
    {
        $this->expectOutputRegex('#GET /v2/pet/' . self::PET_ID . ' HTTP/1.1#');

        $promise = $this->api->getPetByIdAsync(self::PET_ID);
        $promise->wait();
    }

    private function preparePet(): Model\Pet
    {
        $newPetId = self::PET_ID;
        $newPet = new Model\Pet;
        $newPet->setId($newPetId);
        $newPet->setName("PHP Unit Test");
        $newPet->setPhotoUrls(["https://test_php_unit_test.com"]);
        return $newPet;
    }
}
