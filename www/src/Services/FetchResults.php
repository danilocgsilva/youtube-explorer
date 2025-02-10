<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\ClientInterface;

class FetchResults
{
    private array $titlesList = [];

    private int $channelVideoCount;
    
    public function __construct(private string $apiKey, private ClientInterface $httpClient)
    {
    }
    
    public function getByUploadsIds(string $uploadsId)
    {
        $pagination = 50;
        $urlToPaylist = sprintf(
            'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&maxResults=%s&playlistId=%s&key=%s',
            $pagination,
            $uploadsId,
            $this->apiKey
        );
        $response = $this->httpClient->request("GET", $urlToPaylist);
        return $response->getBody()->getContents();
    }

    public function fetch(string $uploadsId): self
    {
        $rawContentsString = $this->getByUploadsIds($uploadsId);
        $contents = json_decode($rawContentsString);

        $this->channelVideoCount = $contents->pageInfo->totalResults;

        foreach ($contents->items as $item) {
            $this->titlesList[] = $item->snippet->title;
        }

        return $this;
    }

    public function getTitlesList(): array
    {
        return $this->titlesList;
    }

    public function getChannelVideoCount(): int
    {
        return $this->channelVideoCount;
    }
}
