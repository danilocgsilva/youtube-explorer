<?php

declare(strict_types=1);

namespace App\Traits;

use App\Services\Fetcher;
use App\Data\FetcheResult;
use Exception;
use Psr\Log\LoggerInterface;

trait FetchOnce
{
    public function fetchSinglePagination(
        string $uploadsId,
        LoggerInterface $logger,
        int $pagination = 50,
        string $nextPageToken = ""
    ): FetcheResult
    {
        $fetcher = new Fetcher(
            $this->apiKey, 
            $this->httpClient, 
            $pagination
        );

        $trial = 0;
        $maxTrial = 3;
        $executed = false;
        $exceptionIfAny = null;
        while (!$executed && $trial < $maxTrial) {
            try {
                $fetcher->fetch($uploadsId, $nextPageToken);
                $executed = true;
            } catch (Exception $e) {
                $logger->warning("Failed to fetch pagination content. Trial: {$trial}.");
                $exceptionIfAny = $e;
                $trial++;
            }
        }
        if (!$executed) {
            throw $exceptionIfAny;
        }

        return $fetcher->getResults();
    }
}
