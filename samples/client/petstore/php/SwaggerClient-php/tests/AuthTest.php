<?php
declare(strict_types=1);

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;
use Swagger\Client\Api\FakeApi;
use Swagger\Client\Api\PetApi;
use Swagger\Client\Model\Pet;

require_once __DIR__ . '/FakeHttpClient.php';

class AuthTest extends TestCase
{
    public function testCustomApiKeyHeader(): void
    {
        $authConfig = new Configuration();
        $authConfig->setApiKey('api_key', '123qwe');

        $fakeHttpClient = new FakeHttpClient();
        $api = new PetApi($fakeHttpClient, $authConfig);
        $api->getPetById(123);

        $headers = $fakeHttpClient->getLastRequest()->getHeaders();

        $this->assertArrayHasKey('api_key', $headers);
        $this->assertEquals(['123qwe'], $headers['api_key']);
    }

    public function testApiToken(): void
    {
        $authConfig = new Configuration();
        $authConfig->setAccessToken('asd123');

        $fakeHttpClient = new FakeHttpClient();
        $api = new PetApi($fakeHttpClient, $authConfig);
        $api->addPet(new Pet());

        $headers = $fakeHttpClient->getLastRequest()->getHeaders();

        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertEquals(['Bearer asd123'], $headers['Authorization']);
    }

    public function testBasicAuth(): void
    {
        $username = 'user';
        $password = 'password';

        $authConfig = new Configuration();
        $authConfig->setUsername($username);
        $authConfig->setPassword($password);

        $fakeHttpClient = new FakeHttpClient();
        $api = new FakeApi($fakeHttpClient, $authConfig);
        $api->testEndpointParameters(123, 100.1, 'ASD_', 'ASD');

        $headers = $fakeHttpClient->getLastRequest()->getHeaders();

        $this->assertArrayHasKey('Authorization', $headers);
        $encodedCredentials = base64_encode("$username:$password");
        $this->assertEquals(["Basic $encodedCredentials"], $headers['Authorization']);
    }
}
