<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'content',
        'user_id',
        'next_repeat_at',
        'stage',
    ];

    protected $casts = [
        'next_repeat_at' => 'datetime',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'card_tag', 'card_id', 'tag_id');
    }
}
