<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use App\Repository\ChannelRepository;
use App\Traits\CaptureChannelTrait;
use App\Traits\FetchOnce;
use App\Traits\GetByUploadsIdsTrait;
use App\Traits\PersistVideosFetchedTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class FetchAllVideos
{
    use GetByUploadsIdsTrait;
    use FetchOnce;
    use PersistVideosFetchedTrait;
    use CaptureChannelTrait;

    private string $nextPageToken = "";

    private int $count = 0;

    public function __construct(
        private string $uploadsId,
        private int $pagination,
        private string $apiKey,
        private WebClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private int $limit = 0
    ) {
    }

    public function fetchAllVideos(
        string $channelSearchTerm, 
        ChannelRepository $channelRepository
    ): array
    {
        $fetchesResults = [];
        while ($results = $this->fetchNext()) {
            $capturedChannel = $this->captureChannel(
                $results, 
                $channelSearchTerm, 
                $channelRepository
            );
            $this->persistChannel($results, $channelSearchTerm);
    
            $this->persistVideos($results, $capturedChannel);
            
            $fetchesResults[] = $results;
        }

        return $fetchesResults;
    }



    private function fetchNext(): FetcheResult|null
    {
        $results = $this->fetchSinglePagination(
            $this->uploadsId,
            $this->logger,
            $this->pagination,
            $this->nextPageToken
        );
        if (
            (!$results->nextPageToken && $this->limit === 0)
            ||
            (!$results->nextPageToken && $this->limit !== 0 && $this->count <= $this->limit)
        ) {
            return null;
        }
        $this->nextPageToken = $results->nextPageToken;
        $this->count++;
        return $results;
    }


}
