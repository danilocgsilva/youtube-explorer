<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Services\Fetch;

final class FetchController extends AbstractController
{
    #[Route('/fetch/{channelHash}', name: 'app_fetch')]
    public function index(string $channelHash, Fetch $fetch): JsonResponse
    {
        /**
         * @var array
         */
        $results = $fetch->fetch($channelHash);

        return $this->json([
            'videos' => $results,
        ]);
    }
}
