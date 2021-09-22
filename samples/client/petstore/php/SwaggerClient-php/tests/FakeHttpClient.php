<?php
declare(strict_types=1);

namespace Swagger\Client;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class FakeHttpClient implements ClientInterface
{
    private ?RequestInterface $request;

    private ?ResponseInterface $response;

    public function getLastRequest(): ?RequestInterface
    {
        return $this->request;
    }

    public function setResponse(ResponseInterface $response = null): void
    {
        $this->response = $response;
    }

    /**
     * Send an HTTP request.
     * @throws GuzzleException
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        $this->request = $request;
        return $this->response ?? new Response(200, [], "{}");
    }

    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        throw new RuntimeException('not implemented');
    }

    public function request($method, $uri, array $options = []): ResponseInterface
    {
        throw new RuntimeException('not implemented');
    }

    public function requestAsync($method, $uri, array $options = []): PromiseInterface
    {
        throw new RuntimeException('not implemented');
    }

    public function getConfig($option = null)
    {
        throw new RuntimeException('not implemented');
    }
}