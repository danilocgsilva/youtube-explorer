<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\Fetcher;
use App\Mapper\GetVideoArray;

trait FetchOnce
{
    public function fetchSinglePagination(
        string $uploadsId,
        string $channelSearchTerm,
        int $pagination
    )
    {
        $fetcher = new Fetcher(
            $this->apiKey, 
            $this->httpClient, 
            $pagination
        );
        $fetcher->fetch($uploadsId);
        
        return $fetcher->getResults();

        // $this->persistChannel($results, $channelSearchTerm);

        // $capturedChannel = $this->captureChannel($results, $channelSearchTerm);

        // $videosArrayGetter = new GetVideoArray($results);
        // $videosArrayGetter->setChannel($capturedChannel);

        // /** @var array<\App\Entity\Video> */
        // $videos = $videosArrayGetter->getVideos();
        // foreach ($videos as $video) {
        //     $this->entityManager->persist($video);
        // }
        // $this->entityManager->flush();

        // return $results;
    }
}
