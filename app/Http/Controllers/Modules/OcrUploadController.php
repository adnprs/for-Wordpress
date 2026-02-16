<?php

namespace App\Http\Controllers\Modules;

use App\Http\Controllers\Controller;
use App\Services\Gemini\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Throwable;

class OcrUploadController extends Controller
{
    public function __construct(private readonly GeminiService $geminiService)
    {
    }

    public function upload(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $apiKey = (string) config('services.gemini.api_key', '');

        if (trim($apiKey) === '') {
            return response()->json([
                'message' => 'Gemini API key is not configured.',
            ], 500);
        }

        /** @var UploadedFile $file */
        $file = $validated['file'];

        try {
            $rawText = $this->extractTextViaOcrSpace($file);

            if (trim($rawText) === '') {
                return response()->json([
                    'message' => 'No text could be extracted from the uploaded file.',
                ], 422);
            }

            $prompt = $this->buildStructuringPrompt($rawText);
            $geminiResponse = $this->geminiService->generateContent($prompt, $apiKey);
            $generatedText = $this->extractGeminiText($geminiResponse);
            $decoded = $this->decodeJsonPayload($generatedText);

            return response()->json([
                'file_name' => $file->getClientOriginalName(),
                'template' => [
                    'job' => $decoded['job'] ?? null,
                    'result' => $decoded['result'] ?? null,
                ],
                'raw_text_excerpt' => mb_substr($rawText, 0, 1000),
                'raw_response' => $generatedText,
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'message' => 'OCR upload processing failed.',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    private function extractTextViaOcrSpace(UploadedFile $file): string
    {
        $ocrApiKey = (string) config('services.ocr.ocr_space_api_key', '');

        if (trim($ocrApiKey) === '') {
            throw new \RuntimeException('OCR_SPACE_API_KEY is not configured.');
        }

        $endpoint = (string) config('services.ocr.ocr_space_endpoint', 'https://api.ocr.space/parse/image');

        $response = Http::timeout(60)
            ->asMultipart()
            ->withHeaders([
                'apikey' => $ocrApiKey,
            ])
            ->attach(
                'file',
                file_get_contents($file->getRealPath()) ?: '',
                $file->getClientOriginalName()
            )
            ->post($endpoint, [
                'language' => 'eng',
                'isOverlayRequired' => 'false',
                'OCREngine' => '2',
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('OCR provider request failed: '.$response->body());
        }

        $payload = $response->json();

        if (! is_array($payload) || ! empty($payload['IsErroredOnProcessing'])) {
            $errorMessage = data_get($payload, 'ErrorMessage.0')
                ?? data_get($payload, 'ErrorMessage')
                ?? 'OCR processing error.';

            throw new \RuntimeException((string) $errorMessage);
        }

        $segments = data_get($payload, 'ParsedResults', []);

        if (! is_array($segments)) {
            return '';
        }

        $texts = collect($segments)
            ->map(fn ($segment) => (string) data_get($segment, 'ParsedText', ''))
            ->filter()
            ->all();

        return trim(implode("\n", $texts));
    }

    private function buildStructuringPrompt(string $rawText): string
    {
        $content = mb_substr($rawText, 0, 20000);

        return <<<PROMPT
You are a data extraction assistant.
From the provided OCR text, structure output into a "Job/Result" template.

STRICT OUTPUT RULES:
1) Return only valid JSON.
2) No markdown, no commentary.
3) Use this exact schema:
{
  "job": "Clear statement of the job/task",
  "result": "Outcome/result details"
}

OCR Text:
{$content}
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
