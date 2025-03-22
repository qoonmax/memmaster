<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use JetBrains\PhpStorm\NoReturn;

class TagController extends Controller
{
    #[NoReturn] public function index(): array
    {
        $tags = Cache::remember('tags_by_user_' . auth()->id(), 3600 * 3, function () {
            return Tag::query()
                ->where('user_id', auth()->id())
                ->whereHas('cards')
                ->get();
        });

        return TagResource::collection($tags)->resolve();
    }
}
