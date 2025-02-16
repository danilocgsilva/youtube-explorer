<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Fetcher;
use App\Services\WebClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use App\Data\FetcheResult;
use App\Data\Video;
use DateTime;
use Override;

class FetcherTest extends TestCase
{
    private Fetcher $fetcher;

    #[Override]
    public function setUp(): void
    {
        $this->fetcher = new Fetcher("", $this->getWebClientMock());
    }

    /**
     * @test
     */
    public function fetch(): void
    {
        $this->assertInstanceOf(Fetcher::class, $this->fetcher->fetch("Ubc123zxyz"));
    }

    /**
     * @test
     */
    public function getResults(): void
    {
        $this->fetcher->fetch("Ubc123zxyz");
        $fetchedResult = $this->fetcher->getResults();

        $this->assertInstanceOf(FetcheResult::class, $fetchedResult);
        $this->assertSame(10, $fetchedResult->channelVideosCount);
        $this->assertSame("Channel title", $fetchedResult->channelTitle);
    }

    /**
     * @test
     */
    public function getResultsCheckingVideosReturned()
    {
        $this->fetcher->fetch("Ubc123zxyz");
        $fetchedResult = $this->fetcher->getResults();
        $videosList = $fetchedResult->videosList;
        /** @var \App\Data\Video */
        $firstVideo = $videosList[0];
    
        $this->assertInstanceOf(Video::class, $firstVideo);
        $this->assertSame("Video title", $firstVideo->videoTitle);
        $this->assertInstanceOf(DateTime::class, $firstVideo->publishTime);
        $this->assertSame("2025-02-10T14:08:48Z", $firstVideo->publishTime->format("Y-m-d\TH:i:s\Z"));
        $this->assertSame("2025-02-10T14:08:48Z", $firstVideo->publishTimeString);
    }

    private function getWebClientMock(): MockObject|WebClientInterface
    {
        $getContentObjectReturn = (object) [
            "pageInfo" => [
                "totalResults" => 10
            ],
            "items" => [
                [
                    "snippet" => [
                        "channelTitle" => "Channel title",
                        "title" => "Video title",
                        "channelId" => "abc1234_YXZyxz"
                    ],
                    "contentDetails" => [
                        "videoPublishedAt" => "2025-02-10T14:08:48Z",
                        "videoId" => "abc_XYZ_0123"
                    ]
                ]
            ]
        ];

        $webClient = $this->getMockBuilder(WebClientInterface::class)
            ->getMock();

        $webClient->expects($this->once())
            ->method("getContentString")
            ->willReturn(
                json_encode($getContentObjectReturn)
            );

        return $webClient;
    }
}
