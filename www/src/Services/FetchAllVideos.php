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

    private $count = 0;

    private string $nextPageToken = "";

    public function __construct(
        private string $uploadsId,
        private int $pagination,
        private string $apiKey,
        private WebClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
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
            // $contents = json_decode($rawContentsString);

            // $this->channelVideoCount = $contents->pageInfo->totalResults;

            // $this->channelName = $contents->items[0]->snippet->channelTitle;

            // $this->channelId = $contents->items[0]->snippet->channelId;

            // foreach ($contents->items as $item) {
            //     $videoLoop = new Video(
            //         $item->snippet->title,
            //         DateTime::createFromFormat("Y-m-d\TH:i:s\Z", $item->contentDetails->videoPublishedAt),
            //         $item->contentDetails->videoPublishedAt,
            //         $item->contentDetails->videoId
            //     );
            //     $this->entityManager->persist($videoLoop);
            // }
            // $this->entityManager->flush();

            // $this->fetchSinglePagination(
            //     $this->uploadsId,
            // );
        }

        return $fetchesResults;
    }



    private function fetchNext(): FetcheResult|null
    {
        // if ($this->count >= 4) {
        //     throw new \Exception("Enough!");
        // }
        // return $this->getByUploadsIds(
        //     $this->uploadsId,
        //     $this->pagination,
        //     $this->apiKey,
        //     $this->httpClient,
        //     $this->nextPageToken
        // );

        $results = $this->fetchSinglePagination(
            $this->uploadsId,
            $this->logger,
            $this->pagination,
            $this->nextPageToken
        );
        if (!$results->nextPageToken) {
            return null;
        }
        $this->nextPageToken = $results->nextPageToken;
        $this->count++;
        return $results;
    }


}
