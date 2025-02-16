<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\FetcheResult;
use Exception;
use App\Services\WebClientInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ChannelSearchHistory;
use DateTime;
use App\Entity\Channel;
use App\Repository\ChannelRepository;

class Fetch
{
    public function __construct(
        private string $apiKey,
        private WebClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private ChannelRepository $channelRepository
    ) {}

    public function fetch(string $channelSearchTerm): FetcheResult
    {
        $uploadsId = $this->getUploads($channelSearchTerm);
        $fetcher = new Fetcher($this->apiKey, $this->httpClient);
        $fetcher->fetch($uploadsId);
        $results = $fetcher->getResults();

        $this->persist($results, $channelSearchTerm);

        $this->captureChannel($results);

        return $results;
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

    private function persist(FetcheResult $results, string $channelSearchTerm): void
    {
        $channelSearchHistory = (new ChannelSearchHistory())
            ->setChannelId($results->channelId)
            ->setChannelName($results->channelTitle)
            ->setSearchTerm($channelSearchTerm)
            ->setWhenFetched(new DateTime());

        $this->entityManager->persist($channelSearchHistory);
        $this->entityManager->flush();
    }

    private function captureChannel(FetcheResult $results)
    {
        $found = $this->channelRepository->findOneBy(["channelId" => $results->channelId]);

        if (!$found) {
            $channel = (new Channel())
                ->setChannelId($results->channelId)
                ->setChannelName($results->channelTitle);

            $this->entityManager->persist($channel);
            $this->entityManager->flush();
        }
    }
}
