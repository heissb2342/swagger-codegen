<?php
declare(strict_types=1);

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;
use Swagger\Client\Api\UserApi;

class UserApiTest extends TestCase
{
    private UserApi $api;

    public function setUp(): void
    {
        $this->api = new Api\UserApi();
    }

    // test login use
    public function testLoginUser(): void
    {
        // initialize the API client
        // login
        $response = $this->api->loginUser('xxxxx', 'yyyyyyyy');

        $this->assertIsString($response);
        $this->assertMatchesRegularExpression(
            '/logged in user session/',
            $response,
            "response string starts with 'logged in user session'"
        );
    }
}
