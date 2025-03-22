<?php

namespace App\Services\OpenAIHelper;

interface OpenAIHelper
{
    public function getTagsFromText(string $text, string $tags): string;
}
