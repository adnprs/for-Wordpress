<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneratedContent extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'app_id',
        'user_id',
        'title',
        'content',
        'status',
        'meta_data',
    ];

    protected function casts(): array
    {
        return [
            'meta_data' => 'array',
        ];
    }

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
