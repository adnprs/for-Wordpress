<?php

namespace App\Services\WordPress;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class WordPressClient
{
    /**
     * Publish a draft post to WordPress using connection details.
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function postDraft(array $data, array|object $connectionDetails): array
    {
        $endpointUrl = rtrim((string) data_get($connectionDetails, 'endpoint_url', ''), '/');
        $username = (string) data_get($connectionDetails, 'username', '');
        $password = $this->resolvePassword(data_get($connectionDetails, 'encrypted_password'));

        if ($endpointUrl === '' || $username === '' || $password === '') {
            throw new InvalidArgumentException('WordPress connection details are incomplete.');
        }

        $defaultCategory = data_get($connectionDetails, 'default_category');
        $payload = [
            'title' => data_get($data, 'title', ''),
            'content' => data_get($data, 'content', ''),
            'excerpt' => data_get($data, 'excerpt'),
            'status' => data_get($data, 'status', 'draft'),
        ];

        if (data_get($data, 'categories') !== null) {
            $payload['categories'] = data_get($data, 'categories');
        } elseif ($defaultCategory !== null && $defaultCategory !== '') {
            $payload['categories'] = [(int) $defaultCategory];
        }

        $response = Http::acceptJson()
            ->withBasicAuth($username, $password)
            ->post("{$endpointUrl}/wp-json/wp/v2/posts", array_filter($payload, static fn ($value) => $value !== null));

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json();
    }

    private function resolvePassword(mixed $password): string
    {
        if (! is_string($password) || trim($password) === '') {
            return '';
        }

        try {
            return Crypt::decryptString($password);
        } catch (\Throwable) {
            return $password;
        }
    }
}
