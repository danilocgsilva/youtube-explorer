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
use GuzzleHttp\Psr7\Response as ClientResponse;
use Symfony\Component\HttpFoundation\Response;
use stdClass;
use App\Tests\Data;

class FetchTest extends KernelTestCase
{
    use ResponseDataMockerTrait;

    private Fetch $fetch;

    private EntityManagerInterface $entityManager;

    private $container;

    /**
     * @var array<\App\Tests\Data>
     */
    private array $data;

    /**
     * @var \App\Tests\Mocks\ClientMock
     */
    protected $clientInterface;

    public function __construct(string|null $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->data = [
            new Data(
                "@mysearchChannel",
                "V9tiHe43FahmXccU54OJ",
                "zNJhf2dMqONQgWNR6FAr",
                "Your Channel Name"
            ),
            new Data(
                "@anotherChannel",
                "sTE0Zw6OH64ACzc7uCrD",
                "PjoY38kGsQW5Sf71Tszk",
                "Another Channel Name"
            ),
        ];
    }

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
        $this->prepareResponsesNewFetch(
            $this->data[0]->channelId, 
            $this->data[0]->uploadId, 
            $this->data[0]->channelName
        );

        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch($this->data[0]->searchTerm);
        $this->assertSame(1, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(1, count($channelRepository->findAll()));
    }

    public function testCaptureOnceTheChannel(): void
    {
        $this->prepareResponsesNewFetch(
            $this->data[0]->channelId, 
            $this->data[0]->uploadId, 
            $this->data[0]->channelName
        );
        $this->prepareResponsesNewFetch(
            $this->data[0]->channelId, 
            $this->data[0]->uploadId, 
            $this->data[0]->channelName
        );

        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch($this->data[0]->searchTerm);
        $this->fetch->fetch($this->data[0]->searchTerm);

        $this->assertSame(2, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(1, count($channelRepository->findAll()));
    }

    public function testCaptureTwoDifferentTheChannels(): void
    {
        $this->prepareResponsesNewFetch(
            $this->data[0]->channelId, 
            $this->data[0]->uploadId, 
            $this->data[0]->channelName
        );
        $this->prepareResponsesNewFetch(
            $this->data[1]->channelId, 
            $this->data[1]->uploadId, 
            $this->data[1]->channelName
        );

        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch($this->data[0]->searchTerm);
        $this->fetch->fetch($this->data[1]->searchTerm);

        $this->assertSame(2, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(2, count($channelRepository->findAll()));
    }

    private function prepareResponsesNewFetch(string $channelId, string $uploadsId, string $channelName)
    {
        $this->prepareMock(
            new ClientResponse(
                Response::HTTP_OK,
                [],
                json_encode($this->mockWegClientResultsGetChannelId($channelId))
            )
        );

        $this->prepareMock(
            new ClientResponse(
                Response::HTTP_OK,
                [],
                json_encode(
                    $this->mockWebClientStringGetUploadsByChannelId(
                        $uploadsId,
                        $channelId
                    )
                )
            )
        );

        $this->prepareMock(
            new ClientResponse(
                Response::HTTP_OK,
                [],
                json_encode(
                    $this->mockWebClientStringGetFileldsFromUploadId(
                        $uploadsId,
                        $channelId,
                        $channelName
                    )
                )
            )
        );
    }

    private function mockWegClientResultsGetChannelId(string $channelId): stdClass
    {
        $data = (object) [
            "items" => [
                [
                    "id" => [
                        "channelId" => $channelId
                    ]
                ]
            ]
        ];

        return $data;
    }

    private function mockWebClientStringGetUploadsByChannelId(string $uploads, string $channelId): stdClass
    {
        $data = (object) [
            "items" => [
                [
                    "id" => [
                        "channelId" => $channelId
                    ],
                    "contentDetails" => [
                        "relatedPlaylists" => [
                            "uploads" => $uploads
                        ],
                        "videoPublishedAt" => "2025-01-05T12:00:12Z",
                        "videoId" => "the_video_id_abc_123"
                    ],
                ]
            ]
        ];

        return $data;
    }

    private function mockWebClientStringGetFileldsFromUploadId(string $uploads, string $channelId, string $channelName): stdClass
    {
        $data = (object) [
            "items" => [
                [
                    "id" => [
                        "channelId" => $channelId
                    ],
                    "contentDetails" => [
                        "relatedPlaylists" => [
                            "uploads" => $uploads
                        ],
                        "videoPublishedAt" => "2025-01-05T12:00:12Z",
                        "videoId" => "the_video_id_abc_123"
                    ],
                    "snippet" => [
                        "channelTitle" => "Your Channel Name",
                        "channelId" => $channelId,
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
