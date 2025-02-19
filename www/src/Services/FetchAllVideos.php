<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use App\Data\FetchMethod;
use App\Entity\MassFetchIteration;
use App\Repository\ChannelRepository;
use App\Traits\CaptureChannelTrait;
use App\Traits\FetchOnce;
use App\Traits\GetByUploadsIdsTrait;
use App\Traits\PersistVideosFetchedTrait;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use DateTime;

class FetchAllVideos
{
    use GetByUploadsIdsTrait;
    use FetchOnce;
    use PersistVideosFetchedTrait;
    use CaptureChannelTrait;

    private string $nextPageToken = "";

    private int $count = 0;

    private FetcheResult $resultsIteration;

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
        $iterationPosition = 1;
        while ($this->fetchNext($pagination, $uploadsId)) {

            $this->persistMassFetchInteration($iterationPosition);

            $capturedChannel = $this->captureChannel(
                $this->resultsIteration, 
                $channelSearchTerm, 
                $channelRepository
            );
            $this->persistChannelSearchHistory(
                $this->resultsIteration, 
                $channelSearchTerm,
                FetchMethod::MASS_FETCH
            );
    
            $this->persistVideos($this->resultsIteration, $capturedChannel);
            
            $fetchesResults[] = $this->resultsIteration;
            $iterationPosition++;
        }
        $this->massFetchManager->finish();

        return $fetchesResults;
    }

    private function fetchNext(int $pagination, string $uploadsId): bool
    {
        $this->resultsIteration = $this->fetchSinglePagination(
            $uploadsId,
            $this->logger,
            $pagination,
            $this->nextPageToken
        );

        $this->nextPageToken = $this->resultsIteration->nextPageToken;

        if (
            (!$this->resultsIteration->nextPageToken && $this->limit === 0)
            ||
            (!$this->resultsIteration->nextPageToken || ($this->limit !== 0 && $this->count > $this->limit))
        ) {
            return false;
        }
        
        $this->count++;
        return true;
    }

    private function persistMassFetchInteration(int $iterationPosition)
    {
        $massFetchIteration = (new MassFetchIteration())
            ->setTime(DateTime::createFromFormat('U.u', (string)microtime(true)))
            ->setNextPageToken($this->resultsIteration->nextPageToken)
            ->setMassFetchJob($this->massFetchManager->getMassFetchJob())
            ->setIterationPosition($iterationPosition);
    
        $this->entityManager->persist($massFetchIteration);
        $this->entityManager->flush();
    }
}
