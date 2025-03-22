<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\AIHelperGenerateTags;
use App\Models\Tag;
use App\Services\OpenAIHelper\OpenAIHelper;
use App\Services\OpenAIHelper\OpenAIHelperFactory;
use Exception;
use Illuminate\Http\JsonResponse;
use JetBrains\PhpStorm\NoReturn;

class AIHelperController extends Controller
{
    private readonly OpenAIHelper $openAIHelper;
    public function __construct(OpenAIHelperFactory $openAIHelperFactory)
    {
        $this->openAIHelper = $openAIHelperFactory->create();
    }

    #[NoReturn] public function generateTags(AIHelperGenerateTags $request): JsonResponse
    {
        try {
            $content = $request->validated('content');

            $tagCollection = Tag::query()->where('user_id', auth()->id())->get();
            $tags = $tagCollection->pluck('name')->implode(', ');

            $tags = $this->openAIHelper->getTagsFromText($content, $tags);

            return response()->json([
                'message' => 'Tags successfully generated',
                'tags' => $tags,
            ]);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to generate tags.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
