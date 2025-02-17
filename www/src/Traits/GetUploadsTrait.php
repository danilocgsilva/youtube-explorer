<?php

declare(strict_types=1);

namespace App\Traits;

use Exception;

trait GetUploadsTrait
{
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

        $contents = json_decode($this->httpClient->getContentString($urlIdList));

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
        $channelNameSeatch = ltrim($channelName, "@");
        $urlChannelId = sprintf(
            "https://www.googleapis.com/youtube/v3/search?part=snippet&q=%s&type=channel&key=%s&maxResults=10",
            $channelNameSeatch,
            $this->apiKey
        );

        $clientContent = $this->httpClient->getContentString($urlChannelId);
        $contents = json_decode($clientContent);

        return $contents->items[0]->id->channelId;
    }
}
