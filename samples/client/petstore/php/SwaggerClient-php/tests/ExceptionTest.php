<?php
declare(strict_types=1);

namespace Swagger\Client;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ExceptionTest extends TestCase
{
    public function testNotFound(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionCode(404);
        $this->expectExceptionMessage('https://petstore.swagger.io/INVALID_URL/store/inventory');

        $config = new Configuration();
        $config->setHost('https://petstore.swagger.io/INVALID_URL');

        $api = new Api\StoreApi(
            new Client(),
            $config
        );
        $api->getInventory();
    }

    public function testWrongHost(): void
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Could not resolve host');

        $config = new Configuration();
        $config->setHost('http://wrong_host.zxc');

        $api = new Api\StoreApi(
            new Client(),
            $config
        );
        $api->getInventory();
    }
}
