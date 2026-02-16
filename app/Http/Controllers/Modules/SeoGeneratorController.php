<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Services\Gemini\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class SeoGeneratorController extends Controller
{
    public function __construct(private readonly GeminiService $geminiService)
    {
    }

    public function preview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'keyword' => ['required', 'string', 'min:2', 'max:150'],
            'tone' => ['required', 'string', 'min:2', 'max:50'],
        ]);

        $apiKey = (string) config('services.gemini.api_key', '');

        if (trim($apiKey) === '') {
            return response()->json([
                'message' => 'Gemini API key is not configured.',
            ], 500);
        }

        $prompt = $this->buildSeoPrompt($validated['keyword'], $validated['tone']);

        try {
            $response = $this->geminiService->generateContent($prompt, $apiKey);
            $generatedText = $this->extractGeminiText($response);

            $decoded = $this->decodeJsonPayload($generatedText);

            if (! is_array($decoded)) {
                return response()->json([
                    'message' => 'Gemini response was not valid JSON.',
                    'raw_response' => $generatedText,
                ], 422);
            }

            return response()->json([
                'keyword' => $validated['keyword'],
                'tone' => $validated['tone'],
                'preview' => [
                    'h1' => $decoded['h1'] ?? null,
                    'h2' => $decoded['h2'] ?? [],
                    'schema' => $decoded['schema'] ?? null,
                ],
                'raw_response' => $generatedText,
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'Failed to generate SEO preview.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    private function buildSeoPrompt(string $keyword, string $tone): string
    {
        return <<<PROMPT
You are an SEO strategist.
Generate SEO assets for the provided keyword and tone.

Keyword: {$keyword}
Tone: {$tone}

STRICT OUTPUT RULES:
1) Return ONLY valid JSON.
2) No markdown, no explanation, no code fences.
3) JSON must follow this structure exactly:
{
  "h1": "single compelling H1 under 65 characters",
  "h2": [
    "5-8 SEO-friendly H2 headings"
  ],
  "schema": {
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "...",
    "description": "...",
    "about": "..."
  }
}

Ensure headings are relevant, unique, and include the keyword naturally.
PROMPT;
    }

    private function extractGeminiText(array $response): string
    {
        return (string) data_get($response, 'candidates.0.content.parts.0.text', '');
    }

    private function decodeJsonPayload(string $text): ?array
    {
        $trimmed = trim($text);
        $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed) ?? $trimmed;
        $trimmed = preg_replace('/\s*```$/', '', $trimmed) ?? $trimmed;

        $decoded = json_decode($trimmed, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
    }
}
