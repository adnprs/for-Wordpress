<?php

namespace App\Services\Gemini;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class GeminiService
{
    /**
     * Generate content using Gemini with a dynamic API key.
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function generateContent(string $prompt, string $apiKey): array
    {
        if (trim($apiKey) === '') {
            throw new InvalidArgumentException('Gemini API key is required.');
        }

        $model = config('services.gemini.model', 'gemini-1.5-flash');

        $response = Http::baseUrl('https://generativelanguage.googleapis.com')
            ->acceptJson()
            ->post("/v1beta/models/{$model}:generateContent?key={$apiKey}", [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json();
    }
}
