<?php

declare(strict_types=1);

namespace App\Tests\Mocks;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client as GuzzleClient;

class ClientMock extends GuzzleClient
{
    /** @var MockHandler */
    protected MockHandler $mockHandler;

    public function __construct()
    {
        $this->mockHandler = new MockHandler();
        $handler = HandlerStack::create($this->mockHandler);
        parent::__construct(['handler' => $handler]);
    }

    /**
     * @param $responses
     */
    public function appendResponse($responses): void
    {
        $this->mockHandler->append(...$responses);
    }
}
