<?php

namespace App\Providers;

use App\Services\Firebase\FirestoreClient;
use App\Services\Gemini\GeminiService;
use App\Services\WordPress\WordPressClient;
use Illuminate\Support\ServiceProvider;

class ContentServiceProvider extends ServiceProvider
{
    /**
     * Register content-related service layer bindings.
     */
    public function register(): void
    {
        $this->app->singleton(GeminiService::class, fn () => new GeminiService());
        $this->app->singleton(WordPressClient::class, fn () => new WordPressClient());
        $this->app->singleton(FirestoreClient::class, fn () => new FirestoreClient());
    }
}
