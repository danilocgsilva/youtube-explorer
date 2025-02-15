<?php

declare(strict_types=1);

namespace App\Tests\Mocks\Services;

use App\Services\WebClientInterface;

class WebClientMock implements WebClientInterface
{
    public function getContentString(string $url): string
    {
        $data = (object) [
            "items" => [
                [
                    "id" => [
                        "channelId" => "chanIdabc123"
                    ],
                    "contentDetails" => [
                        "relatedPlaylists" => [
                            "uploads" => "myMockedUploadId"
                        ],
                        "videoPublishedAt" => "2025-01-05T12:00:12Z",
                        "videoId" => "the_video_id_abc_123"
                    ],
                    "snippet" => [
                        "channelTitle" => "Your Channel Name",
                        "channelId" => "the_channel_id_abc123",
                        "title" => "The Video Title"
                    ]
                ]
            ],
            "pageInfo" => [
                "totalResults" => 1
            ]
        ];
        return json_encode($data);
    }
}
