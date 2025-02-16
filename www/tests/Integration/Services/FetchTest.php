<?php

declare(strict_types=1);

namespace App\Tests\Integration\Services;

use App\Services\Fetch;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\ChannelSearchHistoryRepository;
use App\Tests\TestTraits\ResponseDataMockerTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ChannelRepository;
use App\Services\WebClient;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class FetchTest extends KernelTestCase
{
    use ResponseDataMockerTrait;

    private Fetch $fetch;

    private EntityManagerInterface $entityManager;

    private $container;

    #[Override]
    public function setUp(): void
    {
        $this->container = static::getContainer();
        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->entityManager->getConnection()->beginTransaction();

        $this->fetch = $this->container->get(Fetch::class);
    }
    
    public function testAddOneInDatabase(): void
    {
        $container = static::getContainer();
        $this->changeWebClient($container);

        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository  = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch("@mysearchChannel");
        $this->assertSame(1, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(1, count($channelRepository->findAll()));
    }

    public function testCaptureOnceTheChannel(): void
    {
        $container = static::getContainer();
        $this->changeWebClient($container);

        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository  = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch("@mysearchChannel");
        $this->fetch->fetch("@mysearchChannel");

        $this->assertSame(2, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(1, count($channelRepository->findAll()));
    }

    public function testCaptureTwoDifferenteTheChannels(): void
    {
        $container = static::getContainer();
        $this->changeWebClient($container);
        
        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository  = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch("@mysearchChannel");
        $this->fetch->fetch("@anotherChannel");

        $this->assertSame(2, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(2, count($channelRepository->findAll()));
    }

    private function changeWebClient($container)
    {
        $webClientMocked = $this->getMockBuilder(WebClient::class)
            ->setConstructorArgs([$this->getMockBuilder(ClientInterface::class)->getMock(), "oi2"])
            ->getMock();

        $webClientMocked
            ->method("getContentString")
            ->willReturn($this->mockWebClientString());
        
        $container->set(WebClient::class, $webClientMocked);
    }

    private function mockWebClientString(): string
    {
        $data = (object) [
            "items" => [
                [
                    "id" => [
                        "channelId" => "chanIdabc123"
                    ],
                    "contentDetails" => [
                        "relatedPlaylists" => [
                            "uploads" => "myMockedUploadId"
                        ],
                        "videoPublishedAt" => "2025-01-05T12:00:12Z",
                        "videoId" => "the_video_id_abc_123"
                    ],
                    "snippet" => [
                        "channelTitle" => "Your Channel Name",
                        "channelId" => "the_channel_id_abc123",
                        "title" => "The Video Title"
                    ]
                ]
            ],
            "pageInfo" => [
                "totalResults" => 1
            ]
        ];
        return json_encode($data);
    }
}
