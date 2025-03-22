<?php

namespace App\Services;

use Carbon\Carbon;

class IntervalCalculator
{
    public const MAX_STAGE = 8;

    public function calculate(int $stage = 1): Carbon
    {
        return match ($stage) {
            1 => now()->addDays(7),
            2 => now()->addDays(7),
            3 => now()->addDays(7),
            4 => now()->addDays(7),
            5 => now()->addDays(7),
            6 => now()->addDays(7),
            7 => now()->addDays(7),
            8 => now()->addDays(7),
            default => now()->addDays(1),
        };
    }
}
