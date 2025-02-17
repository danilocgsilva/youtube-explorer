<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\ClientInterface;
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
        $this->logger->info($url);
        $this->logger->info($contentString);

        return $contentString;
    }
}
