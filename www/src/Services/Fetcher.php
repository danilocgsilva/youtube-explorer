<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use App\Services\WebClientInterface;
use App\Data\Video;
use DateTime;
use Exception;
use App\Traits\GetByUploadsIdsTrait;

class Fetcher
{
    use GetByUploadsIdsTrait;
    
    private array $titlesList = [];

    /** @var array<\App\Data\Video> */
    private array $videosList = [];

    private int $channelVideoCount;

    private string $channelName;

    private string $channelId;

    private ?string $nextPageToken;

    public function __construct(
        private string $apiKey, 
        private WebClientInterface $httpClient,
        private int $pagination = 50
    ) { 
        if ($pagination > 50) {
            throw new Exception("The youtube api does not allow a pagination higher than 50.");
        }
        $this->pagination = $pagination;
    }

    public function setPagination(int $pagination): self
    {
        $this->pagination = $pagination;
        
        return $this;
    }

    public function fetch(string $uploadsId, string $pageToken = ""): self
    {
        $rawContentsString = $this->getByUploadsIds(
            $uploadsId,
            $this->pagination,
            $this->apiKey,
            $this->httpClient,
            $pageToken
        );
        $contents = json_decode($rawContentsString);

        $this->channelVideoCount = $contents->pageInfo->totalResults;

        $this->channelName = $contents->items[0]->snippet->channelTitle;

        $this->channelId = $contents->items[0]->snippet->channelId;

        $this->nextPageToken = $contents->nextPageToken ?? "";

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
            $this->channelId,
            $this->nextPageToken
        );
    }
}
