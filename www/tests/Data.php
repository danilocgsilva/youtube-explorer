<?php

declare(strict_types=1);

namespace App\Tests;

class Data
{
    public function __construct(
        public readonly string $searchTerm,
        public readonly string $channelId,
        public readonly string $uploadId,
        public readonly string $channelName,
    ) {}
}
