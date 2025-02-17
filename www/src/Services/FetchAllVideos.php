<?php

declare(strict_types=1);

namespace App\Services;

use App\Traits\FetchOnce;
use App\Traits\GetByUploadsIdsTrait;
use DateTime;
use App\Data\Video;
use Doctrine\ORM\EntityManagerInterface;
use App\Mapper\GetVideoArray;

class FetchAllVideos
{
    use GetByUploadsIdsTrait;
    use FetchOnce;

    private $count = 0;

    public function __construct(
        private string $uploadsId,
        private int $pagination,
        private string $apiKey,
        private WebClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function fetchAllVideos()
    {
        $pageToken = "";
        while ($rawContentsString = $this->fetchNext($pageToken)) {
            $contents = json_decode($rawContentsString);

            $this->channelVideoCount = $contents->pageInfo->totalResults;

            $this->channelName = $contents->items[0]->snippet->channelTitle;

            $this->channelId = $contents->items[0]->snippet->channelId;

            foreach ($contents->items as $item) {
                $videoLoop = new Video(
                    $item->snippet->title,
                    DateTime::createFromFormat("Y-m-d\TH:i:s\Z", $item->contentDetails->videoPublishedAt),
                    $item->contentDetails->videoPublishedAt,
                    $item->contentDetails->videoId
                );
                $this->entityManager->persist($videoLoop);
            }
            $this->entityManager->flush();

            // $this->fetchSinglePagination(
            //     $this->uploadsId,
            // );
        }
    }

    private function fetchNext(string $pageToken): string
    {
        if ($this->count >= 3) {
            return "";
        }
        return $this->getByUploadsIds(
            $this->uploadsId,
            $this->pagination,
            $this->apiKey,
            $this->httpClient,
            $pageToken
        );
    }
}
