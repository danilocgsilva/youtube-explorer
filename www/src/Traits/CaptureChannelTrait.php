<?php

declare(strict_types=1);

namespace App\Traits;

use App\Data\FetcheResult;
use App\Entity\Channel;
use App\Repository\ChannelRepository;
use App\Entity\ChannelSearchHistory;
use DateTime;

trait CaptureChannelTrait
{
    public function captureChannel(
        FetcheResult $results, 
        string $searchTerm,
        ChannelRepository $channelRepository
    ): Channel
    {
        /** @var \App\Entity\Channel */
        $found = $channelRepository->findOneBy(["channelId" => $results->channelId]);

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
}
