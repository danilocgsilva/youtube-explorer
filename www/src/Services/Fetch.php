<?php

declare(strict_types=1);

namespace App\Services;

use App\Repository\ChannelRepository;
use App\Repository\VideoRepository;
use App\Services\WebClientInterface;
use App\Traits\PersistVideosFetchedTrait;
use App\Traits\FetchOnce;
use App\Traits\CaptureChannelTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use App\Traits\GetUploadsTrait;

class Fetch
{
    use FetchOnce;
    use PersistVideosFetchedTrait;
    use CaptureChannelTrait;
    use GetUploadsTrait;

    public function __construct(
        private string $apiKey,
        private WebClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private ChannelRepository $channelRepository,
        private VideoRepository $videoRepository
    ) {
    }

    public function fetch(string $channelSearchTerm, LoggerInterface $logger): mixed
    {
        $uploadsId = $this->getUploads($channelSearchTerm);

        /** @var \App\Data\FetcheResult */
        $results = $this->fetchSinglePagination(
            $uploadsId,
            $logger,
            50
        );

        /** @var \App\Entity\Channel */
        $capturedChannel = $this->captureChannel(
            $results, 
            $channelSearchTerm, 
            $this->channelRepository
        );
        $this->persistChannelSearchHistory($results, $channelSearchTerm);

        $this->persistVideos($results, $capturedChannel);

        return $results;
    }

    private function fetchAll(
        string $uploadsId,
        int $pagination,
        string $apiKey,
        WebClientInterface $webClient,
        EntityManagerInterface $entityManager,
        string $channelSearchTerm,
        LoggerInterface $logger
    ): array
    {
        $fetchAllVideos = new FetchAllVideos(
            $uploadsId,
            $pagination,
            $apiKey,
            $webClient,
            $entityManager,
            $logger
        );

        return $fetchAllVideos->fetchAllVideos(
            $channelSearchTerm, 
            $this->channelRepository
        );
    }
}
