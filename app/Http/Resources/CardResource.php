<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'content' => $this->content,
            'next_repeat_at' => $this->next_repeat_at,
            'stage' => $this->stage,
            'tags' => $this->tags->pluck('name')->implode(', '),
        ];
    }
}
