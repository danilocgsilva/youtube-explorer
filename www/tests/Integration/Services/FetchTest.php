<?php

declare(strict_types=1);

namespace App\Tests\Integration\Services;

use App\Services\Fetch;
use Override;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Repository\ChannelSearchHistoryRepository;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as GuzzleClient;
use App\Tests\TestTraits\ResponseDataMockerTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\WebClientInterface;
use App\Services\WebClient;
use App\Repository\ChannelRepository;

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
        $channelSearchHistoryRepository = $this->container->get(ChannelSearchHistoryRepository::class);
        $channelRepository  = $this->container->get(ChannelRepository::class);

        $this->assertSame(0, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(0, count($channelRepository->findAll()));

        $this->fetch->fetch("@mysearchChannel");
        $this->fetch->fetch("@mysearchChannel");

        $this->assertSame(2, count($channelSearchHistoryRepository->findAll()));
        $this->assertSame(1, count($channelRepository->findAll()));
    }
}
