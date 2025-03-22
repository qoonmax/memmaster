<?php

namespace App\Services\OpenAIHelper;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JetBrains\PhpStorm\NoReturn;

class OpenAIHelperPaid implements OpenAIHelper
{
    /**
     * Получить теги из текста.
     * Принимает текст и теги в виде строки, через запятую.
     *
     * @param string $text
     * @param string $tags
     * @return string
     * @throws GuzzleException
     */
    #[NoReturn] public function getTagsFromText(string $text, string $tags): string
    {

        $client = new Client();
        // TODO: вынести в конфиг
        $apiKey = env('OPEN_AI_KEY');

        $url = 'https://api.openai.com/v1/chat/completions';

        $prompt = "Текст это данные из редактора текста, : \"$text\"\nТеги: " . $tags . "\nОпредели, к каким тегам относится текст. Если тексту подходят другие теги, предложи их. Ответь только тегами через запятую.";

        $response = $client->post($url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-4o-mini',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Ты ассистент, который помогает определять теги для текста.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.7,
            ],
        ]);

        $data = json_decode($response->getBody(), true);

        return $data['choices'][0]['message']['content'];
    }
}
