<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\CategoryPrompt;
use App\Models\PostHistory;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class WorkspaceController extends Controller
{
    public function show(int $id): View
    {
        $app = App::find($id);

        if (! $app) {
            abort(404);
        }

        $categories = CategoryPrompt::where('app_id', $app->id)->pluck('category_name', 'id');
        $postHistories = PostHistory::query()
            ->where('user_id', auth()->id())
            ->where('app_id', $app->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('workspace.wordpress', compact('app', 'categories', 'postHistories'));
    }

    public function generate(Request $request, int $id): View|RedirectResponse
    {
        set_time_limit(120);

        $app = App::find($id);

        if (! $app) {
            abort(404);
        }

        $validated = $request->validate([
            'category' => ['required', 'integer'],
            'keyword' => ['nullable', 'string'],
            'document' => ['nullable', 'file'],
            'url' => ['nullable', 'url'],
        ]);

        if (! $request->filled('keyword') && ! $request->hasFile('document') && ! $request->filled('url')) {
            throw ValidationException::withMessages([
                'keyword' => 'Please provide a keyword, document, or URL to generate content.',
            ]);
        }

        $categoryPrompt = CategoryPrompt::findOrFail($validated['category']);
        $template = $categoryPrompt->prompt_template;

        if ((int) $categoryPrompt->app_id !== (int) $app->id) {
            throw ValidationException::withMessages([
                'category' => 'Selected category prompt template was not found.',
            ]);
        }

        $finalPrompt = $template;

        if ($request->filled('keyword')) {
            $finalPrompt = str_replace('{keyword}', $validated['keyword'], $template);
        }

        $apiKey = env('GEMINI_API_KEY');

        if (! $apiKey) {
            throw ValidationException::withMessages([
                'keyword' => 'Gemini API key is missing. Please set GEMINI_API_KEY in your environment.',
            ]);
        }

        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $mimeType = $file->getMimeType();
            $base64Data = base64_encode(file_get_contents($file->path()));
        }

        try {
            if ($request->filled('url')) {
                $response = Http::timeout(60)->get($validated['url']);

                if (! $response->successful()) {
                    throw ValidationException::withMessages([
                        'url' => 'Unable to fetch content from the provided URL.',
                    ]);
                }

                $extractedText = substr(strip_tags($response->body()), 0, 4000);
                $finalPrompt = "Follow these instructions:\n"
                    . $template
                    . "\n\nUse this source data:\n"
                    . $extractedText;
            }

            if ($request->filled('keyword')) {
                $finalPrompt = "CRITICAL INSTRUCTION: You MUST write this post STRICTLY about the specific topic: '" . $request->keyword . "'. Do NOT invent or write about any other companies, banks, or generic jobs. \n\n" . $finalPrompt;
            }

            $parts = [['text' => $finalPrompt]];

            if (isset($base64Data)) {
                $parts[] = [
                    'inline_data' => [
                        'mime_type' => $mimeType,
                        'data' => $base64Data,
                    ],
                ];
            }

            $response = Http::timeout(240)->withHeaders([
                'Content-Type' => 'application/json',
            ])->post($apiUrl, [
                'contents' => [
                    ['parts' => $parts],
                ],
            ]);
        } catch (ConnectionException $e) {
            return back()->withInput()->with('error', 'The AI is taking too long to process this URL. Please try again or use a smaller source.');
        }

        if (! $response->successful()) {
            throw ValidationException::withMessages([
                'keyword' => 'Gemini API request failed. Please try again.',
            ]);
        }

        $generatedHtml = $response->json('candidates.0.content.parts.0.text');

        if (! $generatedHtml) {
            throw ValidationException::withMessages([
                'keyword' => 'Gemini API returned an empty response. Please try again.',
            ]);
        }

        return view('workspace.result', compact('app', 'generatedHtml'));
    }

    public function publish(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string'],
            'content' => ['required', 'string'],
        ]);

        $app = App::findOrFail($id);

        if (! $app->wp_url || ! $app->wp_username || ! $app->wp_password) {
            return back()->with('error', 'WordPress credentials missing in Enterprise Settings.');
        }

        $endpoint = rtrim($app->wp_url, '/') . '/wp-json/wp/v2/posts';

        $response = Http::timeout(60)
            ->withBasicAuth($app->wp_username, $app->wp_password)
            ->post($endpoint, [
                'title' => $request->title,
                'content' => $request->content,
                'status' => 'draft',
            ]);

        if ($response->successful()) {
            PostHistory::create([
                'user_id' => auth()->id(),
                'app_id' => $app->id,
                'title' => $validated['title'],
                'status' => 'Draft',
            ]);

            return redirect()
                ->route('workspace.show', $app->id)
                ->with('success', '✅ Post successfully pushed to WordPress as a Draft!');
        }

        return back()->with('error', 'WordPress API Error: ' . $response->body());
    }
}