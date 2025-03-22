<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use App\Http\Resources\CardResource;
use App\Models\Card;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\ResponseFactory;

class CardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Card/Index');
    }
    public function repetition(): Response
    {
        return Inertia::render('Card/Repetition');
    }

    public function show(string $slug): Response
    {
        return Inertia::render('Card/Show', [
            'slug' => $slug,
        ]);
    }
}
