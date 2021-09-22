<?php

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;
use Swagger\Client\Api\FakeApi;
use Swagger\Client\Api\UserApi;

require_once __DIR__ . '/FakeHttpClient.php';

class ParametersTest extends TestCase
{
    private FakeHttpClient $fakeHttpClient;

    private FakeApi $fakeApi;

    private UserApi $userApi;

    public function setUp(): void
    {
        $this->fakeHttpClient = new FakeHttpClient();
        $this->fakeApi = new Api\FakeApi($this->fakeHttpClient);
        $this->userApi = new Api\UserApi($this->fakeHttpClient);
    }

    public function testHeaderParam(): void
    {
        $this->fakeApi->testEnumParameters([], [], [], 'something');

        $request = $this->fakeHttpClient->getLastRequest();
        $headers = $request->getHeaders();

        $this->assertArrayHasKey('enum_header_string', $headers);
        $this->assertEquals(['something'], $headers['enum_header_string']);
    }

    public function testHeaderParamCollection(): void
    {
        $this->fakeApi->testEnumParameters([], [], ['string1', 'string2']);

        $request = $this->fakeHttpClient->getLastRequest();
        $headers = $request->getHeaders();

        $this->assertArrayHasKey('enum_header_string_array', $headers);
        $this->assertEquals(['string1,string2'], $headers['enum_header_string_array']);
    }

    public function testInlineAdditionalProperties(): void
    {
        $param = new \stdClass();
        $param->foo = 'bar';
        $this->fakeApi->testInlineAdditionalProperties($param);

        $request = $this->fakeHttpClient->getLastRequest();
        $this->assertSame('{"foo":"bar"}', $request->getBody()->getContents());
    }
}
