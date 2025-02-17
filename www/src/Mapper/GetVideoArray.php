<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Data\FetcheResult;
use App\Entity\Video as VideoEntity;
use App\Data\Video as VideoData;
use App\Entity\Channel;
use DateTime;

class GetVideoArray
{
    private ?Channel $channel;
    
    public function __construct(private FetcheResult $fetcheResult)
    {
    }

    public function setChannel(Channel $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * @return array<\App\Entity\Video>
     */
    public function getVideos()
    {
        return array_map(
            function (VideoData $entry) {
                $video = (new VideoEntity())
                    ->setTitle($entry->videoTitle)
                    ->setPublishingTime($entry->publishTimeString)
                    ->setYoutubeVideoId($entry->videoId)
                    ->setFetchedDate(new DateTime());

                if ($this->channel) {
                    $video->setChannel($this->channel);
                }

                return $video;
            },
            $this->fetcheResult->videosList
        );
    }
}
