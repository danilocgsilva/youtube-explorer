<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\WebClientInterface;
use App\Data\FetcheResult;
use App\Mapper\GetVideoArray;
use App\Entity\Channel;

trait PersistVideosFetchedTrait
{
    public function persistVideos(FetcheResult $results, Channel $channel)
    {
        $videosArrayGetter = new GetVideoArray($results);
        $videosArrayGetter->setChannel($channel);

        /** @var array<\App\Entity\Video> */
        $videos = $videosArrayGetter->getVideos();
        foreach ($videos as $video) {
            $this->entityManager->persist($video);
        }
        $this->entityManager->flush();
    }
}
