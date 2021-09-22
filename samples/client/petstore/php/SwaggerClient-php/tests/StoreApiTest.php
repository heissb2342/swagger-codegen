<?php
declare(strict_types=1);

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;
use Swagger\Client\Api\PetApi;
use Swagger\Client\Api\StoreApi;
use Swagger\Client\Model\Category;
use Swagger\Client\Model\Pet;
use Swagger\Client\Model\Tag;

class StoreApiTest extends TestCase
{
    private StoreApi $api;

    /**
     * Setup before running each test case
     */
    public static function setUpBeforeClass(): void
    {
        // add a new pet (id 10005) to ensure the pet object is available for all the tests
        // new pet
        $id = 10005;
        $pet = new Pet();
        $pet->setId($id);
        $pet->setName('PHP Unit Test');
        $pet->setStatus('available');
        // new tag
        $tag = new Tag();
        $tag->setId($id); // use the same id as pet
        $tag->setName('test php tag');
        // new category
        $category = new Category();
        $category->setId($id); // use the same id as pet
        $category->setName('test php category');

        $pet->setTags([$tag]);
        $pet->setCategory($category);

        $api = new PetApi();
        $api->addPet($pet);
    }

    public function setUp(): void
    {
        $this->api = new StoreApi();
    }

    public function testGetInventory(): void
    {
        $result = $this->api->getInventory();

        $this->assertIsArray($result);
        $this->assertIsInt($result['available']);
    }
}
