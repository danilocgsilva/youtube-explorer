<?php

declare(strict_types=1);

namespace App\Data;

class FetcheResult
{
    public function __construct(
        public readonly int $channelVideosCount,
        public readonly array $videoTitles,
        public readonly string $channelTitle,
        public readonly array $videosList
    ) {}
}
