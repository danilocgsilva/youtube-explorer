<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use App\Services\WebClientInterface;
use App\Data\Video;
use DateTime;

class Fetcher
{
    private array $titlesList = [];

    /** @var array<\App\Data\Video> */
    private array $videosList = [];

    private int $channelVideoCount;

    private string $channelName;

    private string $channelId;
    
    public function __construct(private string $apiKey, private WebClientInterface $httpClient)
    {
    }

    public function fetch(string $uploadsId): self
    {
        $rawContentsString = $this->getByUploadsIds($uploadsId);
        $contents = json_decode($rawContentsString);

        $this->channelVideoCount = $contents->pageInfo->totalResults;

        $this->channelName = $contents->items[0]->snippet->channelTitle;

        $this->channelId = $contents->items[0]->snippet->channelId;

        foreach ($contents->items as $item) {
            $this->videosList[] = new Video(
                $item->snippet->title,
                
               DateTime::createFromFormat("Y-m-d\TH:i:s\Z", $item->contentDetails->videoPublishedAt),
               $item->contentDetails->videoPublishedAt,
               $item->contentDetails->videoId
            );
        }

        return $this;
    }

    public function getResults(): FetcheResult
    {
        return new FetcheResult(
            $this->channelVideoCount,
            $this->titlesList,
            $this->channelName,
            $this->videosList,
            $this->channelId
        );
    }

    private function getByUploadsIds(string $uploadsId): string
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
}
