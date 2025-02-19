<?php

namespace App\MessageHandler;

use App\Services\FetchAllVideos;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\FetchAllVideosFromYoutubeChannel;
use App\Traits\GetUploadsTrait;
use App\Repository\ChannelRepository;
use App\Services\WebClient;

#[AsMessageHandler]
final class FetchAllVideosFromYoutubeChannelHandler
{
    use GetUploadsTrait;

    public function __construct(
        private ChannelRepository $channelRepository,
        private FetchAllVideos $fetchAllVideos,
        private string $apiKey,
        private WebClient $httpClient
    ) { }
    
    public function __invoke(FetchAllVideosFromYoutubeChannel $message): void
    {
        $uploadsId = $uploadsId = $this->getUploads($message->searchTerm);

        $this->fetchAllVideos->setLimit($message->fetchesCount);
        $this->fetchAllVideos->setNextPageToken($message->nextPageToken);

        $this->fetchAllVideos->fetchAllVideos(
            $uploadsId,
            $message->searchTerm, 
            $this->channelRepository,
            30
        );
    }
}
