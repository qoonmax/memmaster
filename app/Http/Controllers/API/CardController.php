<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\Card\IndexRequest;
use App\Http\Requests\API\Card\UpdateRequest;
use App\Http\Resources\CardResource;
use App\Models\Card;
use App\Models\Tag;
use App\Services\IntervalCalculator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;

class CardController extends Controller
{
    #[NoReturn] public function index(IndexRequest $request): array
    {
        $tags = $request->validated('tags');
        $allFilter = $request->validated('filters.all');

        $tagIds = explode(',', $tags);

        // TODO: добавить кэш?
        $cardCollection = Card::query()
            ->where('user_id', auth()->id())
            ->when(!$allFilter, function ($query) {
                $query->where('next_repeat_at', '<=', now());
            })
            ->when($tags, function ($query) use ($tagIds) {
                $query->whereHas('tags', function ($query) use ($tagIds) {
                    $query->whereIn('tags.id', $tagIds);
                });
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        return CardResource::collection($cardCollection)->resolve();
    }

    public function show(string $slug): array
    {
        $card = Card::query()
            ->where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return (new CardResource($card))->resolve();
    }

    /**
     * @throws Exception
     */
    public function update(string $slug, UpdateRequest $request, IntervalCalculator $intervalCalculator): JsonResponse
    {
        // Валидация данных
        $data = $request->validated();

        // Найти карточку по slug
        $card = Card::query()
            ->where('slug', $slug)
            ->where('user_id', auth()->id())
            ->first();

        try {
            DB::beginTransaction();

            // Проверим, нужно ли создавать новую карточку
            if ($card === null) {
                // Создаем новую карточку
                $card = Card::query()->create([
                    'slug' => Str::random(), // Можно вынести в обертку генератора слагов, как указано в TODO
                    'content' => $data['content'],
                    'user_id' => auth()->id(),
                    'next_repeat_at' => $intervalCalculator->calculate(1),
                ]);
            } else {
                // Обновляем существующую карточку
                $card->update([
                    'content' => $data['content'],
                ]);
            }

            // Получаем теги
            $tagsNames = explode(',', $data['tags']);
            // Удалим пробелы
            $tagsNames = array_map('trim', $tagsNames);
            // Удалим пустые теги
            $tagsNames = array_filter($tagsNames, fn($tagName) => $tagName !== '');

            if (count($tagsNames) > 0) {
                Cache::forget('tags_by_user_' . auth()->id());
            }

            // Создадим новые теги
            $tagsIds = [];
            foreach ($tagsNames as $tagName) {
                $tag = Tag::query()
                    ->firstOrCreate([
                        'user_id' => auth()->id(),
                        'name' => $tagName
                    ]);
                $tagsIds[] = $tag->id;
            }

            // Синхронизируем теги
            $card->tags()->sync($tagsIds);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'message' => 'Card updated',
            'card' => (CardResource::make($card))->resolve(),
        ]);
    }

    public function repeatImmediately(string $slug): JsonResponse
    {
        $card = Card::query()
            ->where('slug', $slug)
            ->where('user_id', auth()->id())
            ->first();

        $card->update([
            'next_repeat_at' => now(),
        ]);

        return response()->json([
            'message' => 'Date of repetition updated',
            'card' => (CardResource::make($card))->resolve(),
        ]);
    }

    public function repeat(string $slug, IntervalCalculator $intervalCalculator): JsonResponse
    {
        $card = Card::query()
            ->where('slug', $slug)
            ->where('user_id', auth()->id())
            ->first();

        if ($card->stage >= IntervalCalculator::MAX_STAGE) {
            return response()->json([
                'message' => 'The card is already in it`s final stages',
            ], 422);
        }

        $card->update([
            'stage' => $card->stage + 1,
            'next_repeat_at' => $intervalCalculator->calculate($card->stage + 1),
        ]);

        return response()->json([
            'message' => 'Date of repetition updated',
            'card' => (CardResource::make($card))->resolve(),
        ]);
    }

    public function skip(string $slug, IntervalCalculator $intervalCalculator): JsonResponse
    {
        $card = Card::query()
            ->where('slug', $slug)
            ->where('user_id', auth()->id())
            ->first();

        if ($card->stage >= IntervalCalculator::MAX_STAGE) {
            return response()->json([
                'message' => 'The card is already in it`s final stages',
            ], 422);
        }

        $card->update([
            'next_repeat_at' => $intervalCalculator->calculate($card->stage + 1),
        ]);

        return response()->json([
            'message' => 'Date of repetition updated',
            'card' => (CardResource::make($card))->resolve(),
        ]);
    }

    /**
     * @throws Exception
     */
    public function delete(string $slug): JsonResponse
    {
        // Найти карточку по slug
        $card = Card::query()
            ->where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            DB::beginTransaction();

            $card->tags()->sync([]);
            $card->delete();

            Cache::forget('tags_by_user_' . auth()->id());

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json([
            'message' => 'Card deleted',
        ]);
    }
}
