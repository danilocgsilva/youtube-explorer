<?php

declare(strict_types=1);

namespace App\Data;

enum FetchMethod: string
{
    case SINGLE_FETCH = "SINGLE_FETCH";
    case MASS_FETCH = "MASS_FETCH";
}
