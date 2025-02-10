<?php

declare(strict_types=1);

namespace App\Services;

interface WebClientInterface
{
    public function getContentString(string $url): string;
}
