<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

class WebClient implements WebClientInterface
{
    private ClientInterface $webClient;
    private LoggerInterface $logger;

    public function __construct(ClientInterface $client, LoggerInterface $logger)
    {
        $this->webClient = $client;
        $this->logger = $logger;
    }

    public function getContentString(string $url): string
    {
        $contentFetched = $this->webClient->request("GET", $url);
        $contentString = $contentFetched->getBody()->getContents();
        return $contentString;
    }
}
