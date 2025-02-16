<?php

declare(strict_types=1);

namespace App\Tests\TestTraits;

use stdClass;

trait ResponseDataMockerTrait
{
    public function getResponseMocked(): stdClass
    {
        return new stdClass();
    }
}
