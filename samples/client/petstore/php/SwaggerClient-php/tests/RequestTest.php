<?php
declare(strict_types=1);

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;
use Swagger\Client\Api\FakeApi;

class RequestTest extends TestCase
{
    private FakeApi $api;

    private FakeHttpClient $fakeClient;

    public function setUp(): void
    {
        $this->fakeClient = new FakeHttpClient();
        $this->api = new Api\FakeApi($this->fakeClient);
    }

    public function testFormDataEncodingToJson(): void
    {
        $this->api->testJsonFormData('value', 'value2');

        $request = $this->fakeClient->getLastRequest();
        $contentType = $request->getHeader('Content-Type');
        $this->assertEquals(['application/json'], $contentType);

        $requestContent = $request->getBody()->getContents();

        $expected = json_encode(['param' => 'value', 'param2' => 'value2'], JSON_THROW_ON_ERROR);
        $this->assertEquals($expected, $requestContent);
    }
}
