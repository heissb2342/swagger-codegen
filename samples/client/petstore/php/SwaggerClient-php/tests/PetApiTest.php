<?php
declare(strict_types=1);

namespace Swagger\Client;

use Exception;
use GuzzleHttp\Client;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Swagger\Client\Api\PetApi;
use Swagger\Client\Model\Animal;
use Swagger\Client\Model\ApiResponse;
use Swagger\Client\Model\Cat;
use Swagger\Client\Model\Dog;
use Swagger\Client\Model\Pet;

class PetApiTest extends TestCase
{
    private PetApi $api;

    // add a new pet (id 10005) to ensure the pet object is available for all the tests
    public static function setUpBeforeClass(): void
    {
        // increase memory limit to avoid fatal error due to findPetByStatus
        // returning a lot of data
        ini_set('memory_limit', '256M');

        // enable debugging
        //Configuration::$debug = true;

        // new pet
        $newPetId = 10005;
        $newPet = new Model\Pet;
        $newPet->setId($newPetId);
        $newPet->setName('PHP Unit Test');
        $newPet->setPhotoUrls(['https://test_php_unit_test.com']);
        // new tag
        $tag = new Model\Tag;
        $tag->setId($newPetId); // use the same id as pet
        $tag->setName('test php tag');
        // new category
        $category = new Model\Category;
        $category->setId($newPetId); // use the same id as pet
        $category->setName('test php category');

        $newPet->setTags(array($tag));
        $newPet->setCategory($category);

        $config = new Configuration();
        $client = new Client(['headers' => ['api_key' => 'special-key']]);
        $petApi = new Api\PetApi($client, $config);

        // add a new pet (model)
        [, $status] = $petApi->addPetWithHttpInfo($newPet);
        self::assertEquals(200, $status);
    }

    public function setUp(): void
    {
        $this->api = new Api\PetApi();
    }

    public function testGetPetById(): void
    {
        $petId = 10005;

        $this->spin(function () use ($petId) {
            $pet = $this->api->getPetById($petId);
            $this->assertSame($pet->getId(), $petId);
            $this->assertSame($pet->getName(), 'PHP Unit Test');
            $this->assertSame($pet->getPhotoUrls()[0], 'https://test_php_unit_test.com');
            $this->assertSame($pet->getCategory()->getId(), $petId);
            $this->assertSame($pet->getCategory()->getName(), 'test php category');
            $this->assertSame($pet->getTags()[0]->getId(), $petId);
            $this->assertSame($pet->getTags()[0]->getName(), 'test php tag');
        });
    }

    // test getPetByIdWithHttpInfo with a Pet object (id 10005)
    public function testGetPetByIdWithHttpInfo(): void
    {
        // initialize the API client without host
        $petId = 10005;  // ID of pet that needs to be fetched

        $this->spin(function () use ($petId) {
            /** @var $pet Pet */
            [$pet, $status_code, $response_headers] = $this->api->getPetByIdWithHttpInfo($petId);
            $this->assertSame($pet->getId(), $petId);
            $this->assertSame($pet->getName(), 'PHP Unit Test');
            $this->assertSame($pet->getCategory()->getId(), $petId);
            $this->assertSame($pet->getCategory()->getName(), 'test php category');
            $this->assertSame($pet->getTags()[0]->getId(), $petId);
            $this->assertSame($pet->getTags()[0]->getName(), 'test php tag');
            $this->assertSame($status_code, 200);
            $this->assertSame($response_headers['Content-Type'], ['application/json']);
        });
    }

    public function testFindPetByStatus(): void
    {
        $response = $this->api->findPetsByStatus(['available']);
        $this->assertGreaterThan(0, count($response)); // at least one object returned

        $this->assertSame(get_class($response[0]), Pet::class); // verify the object is Pet
        foreach ($response as $pet) {
            $this->assertSame($pet['status'], 'available');
        }

        $response = $this->api->findPetsByStatus(['unknown_and_incorrect_status']);
        $this->assertCount(0, $response);
    }

    public function testFindPetsByTags(): void
    {
        $response = $this->api->findPetsByTags(['test php tag']);
        $this->assertGreaterThan(0, count($response)); // at least one object returned
        $this->assertSame(get_class($response[0]), Pet::class); // verify the object is Pet

        foreach ($response as $pet) {
            $this->assertSame($pet['tags'][0]['name'], 'test php tag');
        }

        $response = $this->api->findPetsByTags(['unknown_and_incorrect_tag']);
        $this->assertCount(0, $response);
    }

    public function testUpdatePet(): void
    {
        $petId = 10001;
        $updatedPet = new Model\Pet;
        $updatedPet->setId($petId);
        $updatedPet->setName('updatePet');
        $updatedPet->setStatus('pending');
        $this->api->updatePet($updatedPet);

        // verify updated Pet
        $this->spin(function () use ($petId) {
            $result = $this->api->getPetById($petId);
            $this->assertSame($result->getId(), $petId);
            $this->assertSame($result->getStatus(), 'pending');
            $this->assertSame($result->getName(), 'updatePet');
        });
    }

    // test updatePetWithFormWithHttpInfo and verify by the "name" of the response
    public function testUpdatePetWithFormWithHttpInfo(): void
    {
        $petId = 10001;  // ID of pet that needs to be fetched

        // update Pet (form)
        [$update_response, $status_code, $http_headers] = $this->api->updatePetWithFormWithHttpInfo(
            $petId,
            'update pet with form with http info'
        );
        // return nothing (void)
        $this->assertNull($update_response);
        $this->assertSame($status_code, 200);
        $this->assertSame($http_headers['Content-Type'], ['application/json']);

        $this->spin(function () use ($petId) {
            $response = $this->api->getPetById($petId);
            $this->assertSame($response->getId(), $petId);
            $this->assertSame($response->getName(), 'update pet with form with http info');
        });
    }

    // test updatePetWithForm and verify by the "name" and "status" of the response
    public function testUpdatePetWithForm(): void
    {
        $pet_id = 10001;  // ID of pet that needs to be fetched
        $this->api->updatePetWithForm($pet_id, 'update pet with form', 'sold');

        $this->spin(function () use ($pet_id) {
            $response = $this->api->getPetById($pet_id);
            $this->assertSame($response->getId(), $pet_id);
            $this->assertSame($response->getName(), 'update pet with form');
            $this->assertSame($response->getStatus(), 'sold');
        });
    }

    // test addPet and verify by the "id" and "name" of the response
    public function testAddPet(): void
    {
        $new_pet_id = 10006;
        $newPet = new Model\Pet;
        $newPet->setId($new_pet_id);
        $newPet->setName('PHP Unit Test 2');

        // add a new pet (model)
        $this->api->addPet($newPet);

        // verify added Pet
        $response = $this->api->getPetById($new_pet_id);
        $this->assertSame($response->getId(), $new_pet_id);
        $this->assertSame($response->getName(), 'PHP Unit Test 2');
    }

    // test upload file
    public function testUploadFile(): void
    {
        // upload file
        $pet_id = 10001;
        $response = $this->api->uploadFile($pet_id, 'test meta', __DIR__ . '/../composer.json');
        // return ApiResponse
        $this->assertInstanceOf(ApiResponse::class, $response);
    }

    // test empty object serialization
    public function testEmptyPetSerialization(): void
    {
        $new_pet = new Model\Pet;
        // the empty object should be serialised to {}
        $this->assertSame("{}", (string)$new_pet);
    }

    // test inheritance in the model
    public function testInheritance(): void
    {
        $new_dog = new Model\Dog;
        // the object should be an instance of the derived class
        $this->assertInstanceOf(Dog::class, $new_dog);
        // the object should also be an instance of the parent class
        $this->assertInstanceOf(Animal::class, $new_dog);
    }

    // test inheritance constructor is working with data
    // initialization
    public function testInheritanceConstructorDataInitialization(): void
    {
        // initialize the object with data in the constructor
        $data = array(
            'class_name' => 'Dog',
            'breed' => 'Great Dane'
        );
        $new_dog = new Model\Dog($data);

        // the property on the derived class should be set
        $this->assertSame('Great Dane', $new_dog->getBreed());
        // the property on the parent class should be set
        $this->assertSame('Dog', $new_dog->getClassName());
    }

    // test if discriminator is initialized automatically
    public function testDiscriminatorInitialization(): void
    {
        $new_dog = new Model\Dog();
        $this->assertSame('Dog', $new_dog->getClassName());
    }

    // test if ArrayAccess interface works
    public function testArrayStuff(): void
    {
        // create an AnimalFarm which is an object implementing the
        // ArrayAccess interface
        $farm = new Model\AnimalFarm();

        // add some animals to the farm to make sure the ArrayAccess
        // interface works
        $farm[] = new Model\Dog();
        $farm[] = new Model\Cat();
        $farm[] = new Model\Animal();

        // assert we can look up the animals in the farm by array
        // indices (let's try a random order)
        $this->assertInstanceOf(Cat::class, $farm[1]);
        $this->assertInstanceOf(Dog::class, $farm[0]);
        $this->assertInstanceOf(Animal::class, $farm[2]);

        // let's try to `foreach` the animals in the farm and let's
        // try to use the objects we loop through
        foreach ($farm as $animal) {
            $this->assertContains($animal->getClassName(), array('Dog', 'Cat', 'Animal'));
            $this->assertInstanceOf(Animal::class, $animal);
        }
    }

    // test if default values works
    public function testDefaultValues(): void
    {
        // add some animals to the farm to make sure the ArrayAccess
        // interface works
        $dog = new Model\Dog();
        $animal = new Model\Animal();

        // assert we can look up the animals in the farm by array
        // indices (let's try a random order)
        $this->assertSame('red', $dog->getColor());
        $this->assertSame('red', $animal->getColor());
    }

    public function testInvalidArgument(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing the required parameter $status when calling findPetsByStatus');
        // the argument is required, and we must specify one or some from 'available', 'pending', 'sold'
        $this->api->findPetsByStatus([]);
    }

    /**
     * @throws Exception
     */
    private function spin(callable $callable, int $maxWaitTimeSeconds = 10, int $waitingIntervalSeconds = 1): void
    {
        $exception = null;

        for ($index = 0; $index < $maxWaitTimeSeconds; $index++) {
            try {
                $callable($this);
                return;
            } catch (Exception $exception) {
                // only rethrow the latest exception when timing out
            }
            sleep($waitingIntervalSeconds);
        }

        if ($exception) {
            throw $exception;
        }

        $backtrace = debug_backtrace();
        throw new Exception('Timeout thrown by ' . $backtrace[1]['class'] . '::' . $backtrace[1]['function'] . "()\n");
    }
}
