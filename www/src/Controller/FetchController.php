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
        $googleCloudApiKey = $this->getParameter('google_cloud_api_key');
        $apiAddress = "https://www.googleapis.com/youtube/v3/search";
        $apiAddressExample = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id=CHANNEL_ID&key=YOUR_API_KEY";
        $apiAddressExample2 = "https://www.googleapis.com/youtube/v3/channels?id={$channelHash}&key={$googleCloudApiKey}";
        $apiAddressExample3 = "https://www.googleapis.com/youtube/v3/playlistItems?maxResults=50&playlistId=UPLOADS_PLAYLIST_ID&key={$googleCloudApiKey}";
        $apiAddressExample4 = "https://www.googleapis.com/youtube/v3/channels?id={$channelHash}&key={$googleCloudApiKey}";
        $apiAddressExample5 = "https://www.googleapis.com/youtube/v3/channels?id={$channelHash}&key={$googleCloudApiKey};";
        $apiAddressExample6 = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id={$channelHash}&key={$googleCloudApiKey}";
        $apiAddressExample7 = "https://www.googleapis.com/youtube/v3/channels?part=contentDetails&id={$channelHash}&key={$googleCloudApiKey}";
        $apiAddressExample8 = "https://www.googleapis.com/youtube/v3/playlistItems?part=snippet,contentDetails&maxResults=50&playlistId={$channelHash}&key={$googleCloudApiKey}&pageToken=";

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/FetchController.php',
            'channelHash' => $channelHash,
            'sampleAddress' => $apiAddressExample8,
        ]);
    }
}
