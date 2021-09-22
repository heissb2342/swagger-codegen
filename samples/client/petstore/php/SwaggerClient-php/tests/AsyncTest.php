<?php
declare(strict_types=1);

namespace Swagger\Client;

use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\TestCase;
use Swagger\Client\Api\PetApi;
use Swagger\Client\Model\Pet;

class AsyncTest extends TestCase
{
    private const PET_ID = 10005;

    private PetApi $api;

    public function setUp(): void
    {
        $this->api = new Api\PetApi();

        $pet = $this->preparePet();

        $this->api->addPet($pet);
    }

    public function testAsyncRequest(): void
    {
        $promise = $this->api->getPetByIdAsync(10005);

        $promise2 = $this->api->getPetByIdAsync(10005);

        $pet = $promise->wait();
        $pet2 = $promise2->wait();
        $this->assertInstanceOf(Pet::class, $pet);
        $this->assertInstanceOf(Pet::class, $pet2);
    }

    public function testAsyncRequestWithHttpInfo(): void
    {
        $promise = $this->api->getPetByIdAsyncWithHttpInfo(self::PET_ID);

        list($pet, $status, $headers) = $promise->wait();
        $this->assertEquals(200, $status);
        $this->assertIsArray($headers);
        $this->assertInstanceOf(Pet::class, $pet);
    }

    public function testAsyncThrowingException(): void
    {
        $this->expectException(ApiException::class);

        $promise = $this->api->getPetByIdAsync(0);
        $promise->wait();
    }

    public function testAsyncApiExceptionWithoutWaitIsNotThrown(): void
    {
        $promise = $this->api->getPetByIdAsync(0);
        sleep(1);

        $this->assertSame(PromiseInterface::PENDING, $promise->getState());
    }

    public function testAsyncHttpInfoThrowingException(): void
    {
        $this->expectException(ApiException::class);

        $promise = $this->api->getPetByIdAsyncWithHttpInfo(0);
        $promise->wait();
    }

    private function preparePet(): Pet
    {
        $pet = new Model\Pet;
        $pet->setId(self::PET_ID);
        $pet->setName('PHP Unit Test');
        $pet->setPhotoUrls(['https://test_php_unit_test.com']);
        // new tag
        $tag = new Model\Tag;
        $tag->setId(self::PET_ID); // use the same id as pet
        $tag->setName('test php tag');
        // new category
        $category = new Model\Category;
        $category->setId(self::PET_ID); // use the same id as pet
        $category->setName('test php category');

        $pet->setTags(array($tag));
        $pet->setCategory($category);
        return $pet;
    }
}
