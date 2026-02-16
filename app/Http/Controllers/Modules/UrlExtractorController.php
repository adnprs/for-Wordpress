<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Services\Gemini\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Throwable;

class UrlExtractorController extends Controller
{
    public function __construct(private readonly GeminiService $geminiService)
    {
    }

    public function rewrite(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2000'],
            'mode' => ['required', 'in:full_blog,summary_70_words'],
        ]);

        $apiKey = (string) config('services.gemini.api_key', '');

        if (trim($apiKey) === '') {
            return response()->json([
                'message' => 'Gemini API key is not configured.',
            ], 500);
        }

        try {
            $sourceHtml = $this->fetchUrlContents($validated['url']);
            $cleanText = $this->cleanHtmlToText($sourceHtml);

            if (trim($cleanText) === '') {
                return response()->json([
                    'message' => 'No readable content found at the provided URL.',
                ], 422);
            }

            $prompt = $this->buildRewritePrompt($cleanText, $validated['mode']);
            $geminiResponse = $this->geminiService->generateContent($prompt, $apiKey);
            $generatedText = $this->extractGeminiText($geminiResponse);

            return response()->json([
                'url' => $validated['url'],
                'mode' => $validated['mode'],
                'rewrite' => trim($generatedText),
                'source_excerpt' => mb_substr($cleanText, 0, 1200),
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'URL extraction and rewriting failed.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    private function fetchUrlContents(string $url): string
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'User-Agent' => 'CACC-Content-Bot/1.0 (+https://localhost)',
                'Accept' => 'text/html,application/xhtml+xml',
            ])
            ->get($url);

        if ($response->failed()) {
            throw new \RuntimeException('Failed to fetch URL content: '.$response->status());
        }

        return (string) $response->body();
    }

    private function cleanHtmlToText(string $html): string
    {
        $withoutScripts = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', ' ', $html) ?? $html;
        $withoutStyles = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', ' ', $withoutScripts) ?? $withoutScripts;
        $plain = strip_tags($withoutStyles);
        $decoded = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $normalized = preg_replace('/\s+/u', ' ', $decoded) ?? $decoded;

        return trim(mb_substr($normalized, 0, 30000));
    }

    private function buildRewritePrompt(string $sourceText, string $mode): string
    {
        $instruction = $mode === 'summary_70_words'
            ? 'Rewrite into an engaging summary in EXACTLY 70 words.'
            : 'Rewrite into a complete SEO-friendly blog article with clear sections, intro, body, and conclusion.';

        return <<<PROMPT
You are an expert content rewriter.

Task:
{$instruction}

Rules:
1) Keep facts accurate to source.
2) Do not add fabricated claims.
3) Use clean, natural language.
4) Return only rewritten content text (no JSON, no markdown fences).

Source Content:
{$sourceText}
PROMPT;
    }

    private function extractGeminiText(array $response): string
    {
        return (string) data_get($response, 'candidates.0.content.parts.0.text', '');
    }
}
