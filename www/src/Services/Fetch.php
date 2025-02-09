<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\Attribute\AutowireCallable;

class Fetch
{
    public function __construct(
        private string $apiKey,
        private ClientInterface $httpClient
    ) {}

    public function fetch(string $channelId)
    {
        $url = sprintf(
            'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=%s&key=%s',
            $channelId,
            $this->apiKey
        );

        $response = $this->httpClient->request("GET", $url);
        $body = $response->getBody();
        print_r(json_decode((string) $body));
    }

    private function getUploads()
    {

    }
}
