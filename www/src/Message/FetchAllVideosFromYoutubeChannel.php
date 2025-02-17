<?php

namespace App\Message;

final class FetchAllVideosFromYoutubeChannel
{
    public function __construct(
        public readonly string $searchTerm,
    ) {
    }
}
