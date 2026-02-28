<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CategoryPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_id',
        'category_name',
        'prompt_template',
    ];

    public function app(): BelongsTo
    {
        return $this->belongsTo(App::class);
    }
}
