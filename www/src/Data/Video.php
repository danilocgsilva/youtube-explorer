<?php

declare(strict_types=1);

namespace App\Data;


class Video
{
    public function __construct(
        public readonly string $videoTitle
    ) {}
}
