<?php

declare(strict_types=1);

namespace App\Data;

use DateTime;

class Video
{
    public function __construct(
        public readonly string $videoTitle,
        public readonly DateTime $publishTime,
        public readonly string $publishTimeString
    ) {}
}
