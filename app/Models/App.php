<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class App extends Model {
    public const TYPE_WORDPRESS = 'wordpress';
    public const TYPE_ANDROID = 'android';

    protected $fillable = ['name', 'type', 'status', 'ai_key', 'wp_url', 'wp_username', 'wp_password'];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getModuleBadgeAttribute(): string
    {
        return $this->type === self::TYPE_ANDROID ? 'Android App' : 'WordPress Site';
    }

    public function categoryPrompts(): HasMany
    {
        return $this->hasMany(CategoryPrompt::class);
    }

    public function postHistories(): HasMany
    {
        return $this->hasMany(PostHistory::class);
    }
}