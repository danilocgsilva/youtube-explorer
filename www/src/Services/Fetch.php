<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use GuzzleHttp\ClientInterface;
use Exception;

class Fetch
{
    public function __construct(
        private string $apiKey,
        private ClientInterface $httpClient
    ) {}

    public function fetch(string $youtubeChannel): FetcheResult
    {
        $uploadsId = $this->getUploads($youtubeChannel);

        $fetcher = new Fetcher($this->apiKey, $this->httpClient);

        $fetcher->fetch($uploadsId);

        return $fetcher->getResults();
    }

    private function getUploads(string $youtubeChannel)
    {
        if ($this->isChannelName($youtubeChannel)) {
            $channelId = $this->getChannelIdByName($youtubeChannel);
        } else {
            $channelId = $youtubeChannel;
        }

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

    private function isChannelName(string $youtubeChannel)
    {
        return $youtubeChannel[0] === "@";
    }

    private function getChannelIdByName(string $channelName): string
    {
        $urlChannelId = sprintf(
            "https://www.googleapis.com/youtube/v3/search?part=snippet&q=%s&type=channel&key=%s&maxResults=1",
            $channelName,
            $this->apiKey
        );
        $response = $this->httpClient->request("GET", $urlChannelId);
        $contents = json_decode($response->getBody()->getContents());

        return $contents->items[0]->id->channelId;
    }
}
