<?php

namespace App\Tests\Services;

use PHPUnit\Framework\TestCase;
use App\Services\Fetcher;
use App\Services\WebClientInterface;
use PHPUnit\Framework\MockObject\MockObject;
use stdClass;
use App\Data\FetcheResult;

class FetcherTest extends TestCase
{
    private Fetcher $fetcher;

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
        $this->assertSame("Video title", $fetchedResult->videoTitles[0]);
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
                        "title" => "Video title"
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
