<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\ClientInterface;
use Exception;

class Fetch
{
    public function __construct(
        private string $apiKey,
        private ClientInterface $httpClient
    ) {}

    public function fetch(string $channelId)
    {
        $uploadsId = $this->getUploads($channelId);

        $videos = [];
        $rawContentsString = $this->fetchResults($uploadsId);

        $contents = json_decode($rawContentsString);
        foreach ($contents->items as $item) {
            $videos[] = $item->snippet->title;
        }

        return $videos;
    }

    private function getUploads(string $channelId)
    {
        $urlIdList = sprintf(
            'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=%s&key=%s',
            $channelId,
            $this->apiKey
        );

        $response = $this->httpClient->request("GET", $urlIdList);
        $contents = json_decode($response->getBody()->getContents());

        if (!empty($contents->items)) {
            return $contents->items[0]->contentDetails->relatedPlaylists->uploads;
        }
        throw new Exception('Could not get uploads playlist ID');
    }
 
    public function fetchResults(string $uploadsId): string
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
}
