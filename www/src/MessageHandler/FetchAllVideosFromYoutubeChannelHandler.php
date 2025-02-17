<?php

namespace App\MessageHandler;

use App\Services\FetchAllVideos;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Message\FetchAllVideosFromYoutubeChannel;
use App\Services\WebClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use App\Traits\GetUploadsTrait;
use App\Repository\ChannelRepository;

#[AsMessageHandler]
final class FetchAllVideosFromYoutubeChannelHandler
{
    use GetUploadsTrait;

    public function __construct(
        private string $apiKey,
        private WebClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private ChannelRepository $channelRepository
    )
    {
    }
    
    public function __invoke(FetchAllVideosFromYoutubeChannel $message): void
    {
        $uploadsId = $uploadsId = $this->getUploads($message->searchTerm);
        
        $fetchAllVideos = new FetchAllVideos(
            $uploadsId,
            30, 
            $this->apiKey,
            $this->httpClient,
            $this->entityManager,
            $this->logger
        );

        $fetchAllVideos->fetchAllVideos(
            $message->searchTerm, 
            $this->channelRepository
        );
    }
}
