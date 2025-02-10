<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use App\Services\WebClientInterface;

class Fetcher
{
    private array $titlesList = [];

    private int $channelVideoCount;

    private string $channelName;
    
    public function __construct(private string $apiKey, private WebClientInterface $httpClient)
    {
    }
    
    public function getByUploadsIds(string $uploadsId): string
    {
        $pagination = 50;
        $urlToPaylist = sprintf(
            'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&maxResults=%s&playlistId=%s&key=%s',
            $pagination,
            $uploadsId,
            $this->apiKey
        );
        return $this->httpClient->getContentString($urlToPaylist);
    }

    public function fetch(string $uploadsId): self
    {
        $rawContentsString = $this->getByUploadsIds($uploadsId);
        $contents = json_decode($rawContentsString);

        $this->channelVideoCount = $contents->pageInfo->totalResults;

        $this->channelName = $contents->items[0]->snippet->channelTitle;

        foreach ($contents->items as $item) {
            $this->titlesList[] = $item->snippet->title;
        }

        return $this;
    }

    public function getResults(): FetcheResult
    {
        return new FetcheResult(
            $this->channelVideoCount,
            $this->titlesList,
            $this->channelName
        );
    }
}
