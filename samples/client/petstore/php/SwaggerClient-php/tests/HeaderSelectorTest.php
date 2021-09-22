<?php
declare(strict_types=1);

namespace Swagger\Client;

use PHPUnit\Framework\TestCase;

class HeaderSelectorTest extends TestCase
{
    public function testSelectingHeaders(): void
    {
        $selector = new HeaderSelector();
        $headers = $selector->selectHeaders([
            'application/xml',
            'application/json'
        ], []);
        $this->assertSame('application/json', $headers['Accept']);

        $headers = $selector->selectHeaders([], []);
        $this->assertArrayNotHasKey('Accept', $headers);

        $header = $selector->selectHeaders([
            'application/yaml',
            'application/xml'
        ], []);
        $this->assertSame('application/yaml,application/xml', $header['Accept']);

        // test selectHeaderContentType
        $headers = $selector->selectHeaders([], [
            'application/xml',
            'application/json'
        ]);
        $this->assertSame('application/json', $headers['Content-Type']);

        $headers = $selector->selectHeaders([], []);
        $this->assertSame('application/json', $headers['Content-Type']);
        $headers = $selector->selectHeaders([], [
            'application/yaml',
            'application/xml'
        ]);
        $this->assertSame('application/yaml,application/xml', $headers['Content-Type']);
    }

    public function testSelectingHeadersForMultipartBody(): void
    {
        // test selectHeaderAccept
        $selector = new HeaderSelector();
        $headers = $selector->selectHeadersForMultipart([
            'application/xml',
            'application/json'
        ]);
        $this->assertSame('application/json', $headers['Accept']);
        $this->assertArrayNotHasKey('Content-Type', $headers);

        $headers = $selector->selectHeadersForMultipart([]);
        $this->assertArrayNotHasKey('Accept', $headers);
        $this->assertArrayNotHasKey('Content-Type', $headers);
    }
}
