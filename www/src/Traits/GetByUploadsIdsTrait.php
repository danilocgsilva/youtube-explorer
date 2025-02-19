<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\WebClientInterface;

trait GetByUploadsIdsTrait
{
    public function getByUploadsIds(
        string $uploadsId,
        int $pagination,
        string $apiKey,
        WebClientInterface $httpClient,
        string $pageToken = ""
    ): string
    {
        $urlToPaylist = sprintf(
            'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&maxResults=%s&playlistId=%s&key=%s',
            $pagination,
            $uploadsId,
            $apiKey
        );

        if (!empty($pageToken)) {
            $urlToPaylist .= "&pageToken={$pageToken}";
        }
        
        return $httpClient->getContentString($urlToPaylist);
    }
}
