<?php

declare(strict_types=1);

namespace App\Tests\Mocks;

use Psr\Log\LoggerInterface;

class LoggerMock implements LoggerInterface
{
    public function emergency(string|\Stringable $message, array $context = []): void
    {

    }

    /** @inheritDoc */
    public function alert(string|\Stringable $message, array $context = []): void
    {

    }

    /** @inheritDoc */
    public function critical(string|\Stringable $message, array $context = []): void
    {

    }

    /** @inheritDoc */
    public function error(string|\Stringable $message, array $context = []): void
    {

    }

    /** @inheritDoc */
    public function warning(string|\Stringable $message, array $context = []): void
    {

    }

    /** @inheritDoc */
    public function notice(string|\Stringable $message, array $context = []): void
    {

    }

    /** @inheritDoc */
    public function info(string|\Stringable $message, array $context = []): void
    {

    }

    /** @inheritDoc */
    public function debug(string|\Stringable $message, array $context = []): void
    {

    }

    /** @inheritDoc */
    public function log($level, string|\Stringable $message, array $context = []): void
    {

    }
}