<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

class WebClient implements ClientInterface
{
    private ClientInterface $webClient;
    private LoggerInterface $logger;
    
    public function __construct(ClientInterface $client, LoggerInterface $logger)
    {
        $this->webClient = $client;
        $this->logger = $logger;
    }

    /**
     * @inheritDoc
     */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return $this->webClient->send($request, $options);
    }

    /**
     * @inheritDoc
     */
    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        return $this->webClient->sendAsync($request, $options);
    }

    /**
     * @inheritDoc
     */
    public function request(string $method, $uri, array $options = []): ResponseInterface
    {
        return $this->webClient->request($method, $uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function requestAsync(string $method, $uri, array $options = []): PromiseInterface
    {
        return $this->webClient->requestAsync($method, $uri, $options);
    }

    /**
     * @inheritDoc
     */
    public function getConfig(?string $option = null)
    {
        return $this->webClient->getConfig($option);
    }
}
