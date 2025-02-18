<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use App\Data\FetchMethod;
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
        private string $apiKey,
        private WebClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger,
        private MassFetchManager $massFetchManager,
        private int $limit = 0
    ) {
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;
        
        return $this;
    }

    public function fetchAllVideos(
        string $uploadsId,
        string $channelSearchTerm, 
        ChannelRepository $channelRepository,
        int $pagination = 30
    ): array
    {
        $fetchesResults = [];
        $this->massFetchManager->start();
        while ($results = $this->fetchNext($pagination, $uploadsId)) {
            $capturedChannel = $this->captureChannel(
                $results, 
                $channelSearchTerm, 
                $channelRepository
            );
            $this->persistChannelSearchHistory(
                $results, 
                $channelSearchTerm,
                FetchMethod::MASS_FETCH
            );
    
            $this->persistVideos($results, $capturedChannel);
            
            $fetchesResults[] = $results;
        }
        $this->massFetchManager->finish();

        return $fetchesResults;
    }



    private function fetchNext(int $pagination, string $uploadsId): FetcheResult|null
    {
        $results = $this->fetchSinglePagination(
            $uploadsId,
            $this->logger,
            $pagination,
            $this->nextPageToken
        );
        if (
            (!$results->nextPageToken && $this->limit === 0)
            ||
            (!$results->nextPageToken || ($this->limit !== 0 && $this->count >= $this->limit))
        ) {
            return null;
        }
        $this->nextPageToken = $results->nextPageToken;
        $this->count++;
        return $results;
    }
}
