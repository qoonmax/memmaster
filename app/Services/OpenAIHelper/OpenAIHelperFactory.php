<?php

namespace App\Services\OpenAIHelper;

class OpenAIHelperFactory
{
    public function create(): OpenAIHelper
    {
        return match (config('app.env')) {
            'production', 'development' => new OpenAIHelperPaid(),
            default => new OpenAIHelperFree(),
        };
    }
}
