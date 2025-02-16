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
use App\Tests\Mocks\LoggerMock;
use GuzzleHttp\Psr7\Response as ClientResponse;
use Symfony\Component\HttpFoundation\Response;
use stdClass;

class FetchTest extends KernelTestCase
{
    use ResponseDataMockerTrait;

    private Fetch $fetch;

    private EntityManagerInterface $entityManager;

    private $container;

    /**
     * @var \App\Tests\Mocks\ClientMock
     */
    protected $clientInterface;

    #[Override]
    public function setUp(): void
    {
        $this->container = static::getContainer();
        $this->clientInterface = $this->container->get('guzzle_http.client');


        $this->entityManager = self::getContainer()->get('doctrine')->getManager();
        $this->entityManager->getConnection()->beginTransaction();

        $this->fetch = $this->container->get(Fetch::class);
    }

    public function testAddOneInDatabase(): void
    {
        $this->prepareResponsesNewFetch();

        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch("@mysearchChannel");
        $this->assertSame(1, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(1, count($channelRepository->findAll()));
    }

    public function testCaptureOnceTheChannel(): void
    {
        // $container = static::getContainer();
        // $this->changeWebClient($container);

        $this->prepareResponsesNewFetch();
        $this->prepareResponsesNewFetch();

        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch("@mysearchChannel");
        $this->fetch->fetch("@mysearchChannel");

        $this->assertSame(2, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(1, count($channelRepository->findAll()));
    }

    public function testCaptureTwoDifferenteTheChannels(): void
    {
        // $container = static::getContainer();
        // $this->changeWebClient($container);

        $this->prepareResponsesNewFetch();
        $this->prepareResponsesNewFetch();

        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch("@mysearchChannel");
        $this->fetch->fetch("@anotherChannel");

        $this->assertSame(2, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(2, count($channelRepository->findAll()));
    }

    private function prepareResponsesNewFetch()
    {
        $responseUpload = new ClientResponse(Response::HTTP_OK, [], json_encode($this->mockWebClientStringGetUploads()));
        $this->prepareMock($responseUpload);

        $responseData = new ClientResponse(Response::HTTP_OK, [], json_encode($this->mockWebClientStringGetData()));
        $this->prepareMock($responseData);

        $responseAllData = new ClientResponse(Response::HTTP_OK, [], json_encode($this->mockWebClientStringAllData()));
        $this->prepareMock($responseAllData);
    }

    private function mockWebClientStringGetUploads(): stdClass
    {

        $data = (object) [
            "items" => [
                [
                    "id" => [
                        "channelId" => "chanIdabc123"
                    ]
                ]
            ]
        ];

        return $data;
    }

    private function mockWebClientStringGetData(): stdClass
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
                ]
            ]
        ];

        return $data;
    }

    private function mockWebClientStringAllData(): stdClass
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

        return $data;
    }

    protected function prepareMock($response)
    {
        $this->clientInterface->appendResponse([$response]);
    }
}
