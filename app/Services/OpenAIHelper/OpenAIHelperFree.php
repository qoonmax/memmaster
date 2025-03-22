<?php

namespace App\Services\OpenAIHelper;

/**
 * Класс заглушка, чтобы экономить деньги на платных запросах к OpenAI.
 */
class OpenAIHelperFree implements OpenAIHelper
{
    public function getTagsFromText(string $text, string $tags): string
    {
        return 'fruit, backend, networks, dinosaurs';
    }
}
