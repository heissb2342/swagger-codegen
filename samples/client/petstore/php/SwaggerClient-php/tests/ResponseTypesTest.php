<?php
declare(strict_types=1);

namespace Swagger\Client;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Swagger\Client\Api\PetApi;
use Swagger\Client\Model\Pet;

require_once __DIR__ . '/FakeHttpClient.php';

class ResponseTypesTest extends TestCase
{
    private PetApi $api;

    private FakeHttpClient $fakeHttpClient;

    public function setUp(): void
    {
        $this->fakeHttpClient = new FakeHttpClient();
        $this->api = new PetApi($this->fakeHttpClient);
    }

    public function testDefined200ReturnType(): void
    {
        $this->fakeHttpClient->setResponse(new Response(200, [], json_encode([], JSON_THROW_ON_ERROR)));
        $result = $this->api->getPetById(123);

        $this->assertInstanceOf(Pet::class, $result);
    }

    public function testDefault2xxReturnType(): void
    {
        $this->fakeHttpClient->setResponse(new Response(255, [], json_encode([], JSON_THROW_ON_ERROR)));
        $result = $this->api->getPetById(123);

        $this->assertInstanceOf(Pet::class, $result);
    }

    public function testDefinedErrorException(): void
    {
        $statusCode = 400;
        $this->expectException(ApiException::class);
        $this->expectExceptionCode($statusCode);

        $this->fakeHttpClient->setResponse(new Response($statusCode, [], '{}'));
        $this->api->getPetById(123);
    }

    public function testDefaultErrorException(): void
    {
        $statusCode = 404;
        $this->expectException(ApiException::class);
        $this->expectExceptionCode($statusCode);

        $this->fakeHttpClient->setResponse(new Response($statusCode, [], '{}'));
        $this->api->getPetById(123);
    }

    public function testDefaultErrorResponseObject(): void
    {
        $result = new \stdClass();
        try {
            $this->fakeHttpClient->setResponse(new Response(404, [], '{}'));
            $this->api->getPetById(123);
        } catch (ApiException $e) {
            $result = $e->getResponseObject();
        }

        $this->assertNull($result);
    }
}
