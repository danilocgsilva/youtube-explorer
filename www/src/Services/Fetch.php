<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use App\Mapper\GetVideoArray;
use App\Services\WebClientInterface;
use App\Traits\PersistVideosFetchedTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ChannelSearchHistory;
use App\Entity\Channel;
use App\Repository\ChannelRepository;
use App\Repository\VideoRepository;
use Exception;
use DateTime;
use App\Traits\FetchOnce;

class Fetch
{
    use FetchOnce;
    use PersistVideosFetchedTrait;

    public function __construct(
        private string $apiKey,
        private WebClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private ChannelRepository $channelRepository,
        private VideoRepository $videoRepository
    ) {
    }

    public function fetch(string $channelSearchTerm): mixed
    {
        $pagination = 50;
        
        $uploadsId = $this->getUploads($channelSearchTerm);

        // $this->fetchAll(
        //     $uploadsId,
        //     $pagination,
        //     $this->apiKey,w
        //     $this->httpClient,
        //     $this->entityManager
        // );

        /** @var \App\Data\FetcheResult */
        $results = $this->fetchSinglePagination(
            $uploadsId,
            $channelSearchTerm,
            $pagination
        );

        /** @var \App\Entity\Channel */
        $capturedChannel = $this->captureChannel($results, $channelSearchTerm);
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
        EntityManagerInterface $entityManager
    ): void
    {
        $fetchAllVideos = new FetchAllVideos(
            $uploadsId,
            $pagination,
            $apiKey,
            $webClient,
            $entityManager
        );
        $fetchAllVideos->fetchAllVideos();
    }

    private function getUploads(string $youtubeChannel)
    {
        if ($this->isChannelName($youtubeChannel)) {
            $channelId = $this->getChannelIdByName($youtubeChannel);
        } else {
            $channelId = $youtubeChannel;
        }

        $urlIdList = sprintf(
            'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=%s&key=%s',
            $channelId,
            $this->apiKey
        );

        $contents = json_decode($this->httpClient->getContentString($urlIdList));

        if (!empty($contents->items)) {
            return $contents->items[0]->contentDetails->relatedPlaylists->uploads;
        }
        throw new Exception('Could not get uploads playlist ID');
    }

    private function isChannelName(string $youtubeChannel)
    {
        return $youtubeChannel[0] === "@";
    }

    private function getChannelIdByName(string $channelName): string
    {
        $channelNameSeatch = ltrim($channelName, "@");
        $urlChannelId = sprintf(
            "https://www.googleapis.com/youtube/v3/search?part=snippet&q=%s&type=channel&key=%s&maxResults=10",
            $channelNameSeatch,
            $this->apiKey
        );

        $clientContent = $this->httpClient->getContentString($urlChannelId);
        $contents = json_decode($clientContent);

        return $contents->items[0]->id->channelId;
    }

    private function persistChannel(FetcheResult $results, string $channelSearchTerm): void
    {
        $channelSearchHistory = (new ChannelSearchHistory())
            ->setChannelId($results->channelId)
            ->setChannelName($results->channelTitle)
            ->setSearchTerm($channelSearchTerm)
            ->setWhenFetched(new DateTime());

        $this->entityManager->persist($channelSearchHistory);
        $this->entityManager->flush();
    }

    private function captureChannel(FetcheResult $results, string $searchTerm): Channel
    {
        /** @var \App\Entity\Channel */
        $found = $this->channelRepository->findOneBy(["channelId" => $results->channelId]);

        if (!$found) {
            $channel = $this->buildChannelEntity($results, $searchTerm);

            $this->entityManager->persist($channel);
            $this->entityManager->flush();

            return $channel;
        } else {
            if ($found->getChannelAlias() !== $searchTerm) {
                $found->setChannelAlias($searchTerm);
                $this->entityManager->flush();
            }
        }

        return $found;
    }

    private function buildChannelEntity(FetcheResult $results, string $searchTerm): Channel
    {
        $channel = (new Channel())
            ->setChannelId($results->channelId)
            ->setChannelName($results->channelTitle);

        if ($searchTerm[0] === "@") {
            $channel->setChannelAlias($searchTerm);
        }

        return $channel;
    }
}
