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
        // $pagination = 50;
        
        $uploadsId = $this->getUploads($channelSearchTerm);

        // $resultsList = $this->fetchAll(
        //     $uploadsId,
        //     30,
        //     $this->apiKey,
        //     $this->httpClient,
        //     $this->entityManager,
        //     $channelSearchTerm,
        //     $logger
        // );
        // $results = $resultsList[0];

        /** @var \App\Data\FetcheResult */
        $results = $this->fetchSinglePagination(
            $uploadsId,
            $logger,
            50
        );

        /** @var \App\Entity\Channel */
        $capturedChannel = $this->captureChannel($results, $channelSearchTerm, $this->channelRepository);
        $this->persistChannel($results, $channelSearchTerm);

        $this->persistVideos($results, $capturedChannel);

        // $videosArrayGetter = new GetVideoArray($results);
        // $videosArrayGetter->setChannel($capturedChannel);

        // /** @var array<\App\Entity\Video> */
        // $videos = $videosArrayGetter->getVideos();
        // foreach ($videos as $video) {
        //     $this->entityManager->persist($video);
        // }
        // $this->entityManager->flush();

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

    // private function getUploads(string $youtubeChannel)
    // {
    //     if ($this->isChannelName($youtubeChannel)) {
    //         $channelId = $this->getChannelIdByName($youtubeChannel);
    //     } else {
    //         $channelId = $youtubeChannel;
    //     }

    //     $urlIdList = sprintf(
    //         'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=%s&key=%s',
    //         $channelId,
    //         $this->apiKey
    //     );

    //     $contents = json_decode($this->httpClient->getContentString($urlIdList));

    //     if (!empty($contents->items)) {
    //         return $contents->items[0]->contentDetails->relatedPlaylists->uploads;
    //     }
    //     throw new Exception('Could not get uploads playlist ID');
    // }

    // private function isChannelName(string $youtubeChannel)
    // {
    //     return $youtubeChannel[0] === "@";
    // }

    // private function getChannelIdByName(string $channelName): string
    // {
    //     $channelNameSeatch = ltrim($channelName, "@");
    //     $urlChannelId = sprintf(
    //         "https://www.googleapis.com/youtube/v3/search?part=snippet&q=%s&type=channel&key=%s&maxResults=10",
    //         $channelNameSeatch,
    //         $this->apiKey
    //     );

    //     $clientContent = $this->httpClient->getContentString($urlChannelId);
    //     $contents = json_decode($clientContent);

    //     return $contents->items[0]->id->channelId;
    // }

    // private function persistChannel(FetcheResult $results, string $channelSearchTerm): void
    // {
    //     $channelSearchHistory = (new ChannelSearchHistory())
    //         ->setChannelId($results->channelId)
    //         ->setChannelName($results->channelTitle)
    //         ->setSearchTerm($channelSearchTerm)
    //         ->setWhenFetched(new DateTime());

    //     $this->entityManager->persist($channelSearchHistory);
    //     $this->entityManager->flush();
    // }

    // private function captureChannel(FetcheResult $results, string $searchTerm): Channel
    // {
    //     /** @var \App\Entity\Channel */
    //     $found = $this->channelRepository->findOneBy(["channelId" => $results->channelId]);

    //     if (!$found) {
    //         $channel = $this->buildChannelEntity($results, $searchTerm);

    //         $this->entityManager->persist($channel);
    //         $this->entityManager->flush();

    //         return $channel;
    //     } else {
    //         if ($found->getChannelAlias() !== $searchTerm) {
    //             $found->setChannelAlias($searchTerm);
    //             $this->entityManager->flush();
    //         }
    //     }

    //     return $found;
    // }
}
