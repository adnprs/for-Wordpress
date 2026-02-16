<?php

namespace App\Services\Firebase;

use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use InvalidArgumentException;

class FirestoreClient
{
    /**
     * Push a short job payload to Firestore collection: jobs_short.
     *
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function pushJobShort(array $data, string $configPath): array
    {
        $serviceAccount = $this->loadServiceAccount($configPath);
        $accessToken = $this->issueAccessToken($serviceAccount);

        $projectId = $serviceAccount['project_id'];
        $collection = config('services.firestore.jobs_short_collection', 'jobs_short');
        $documentId = (string) ($data['id'] ?? $data['job_id'] ?? '');

        $url = "https://firestore.googleapis.com/v1/projects/{$projectId}/databases/(default)/documents/{$collection}";

        $response = Http::acceptJson()
            ->withToken($accessToken)
            ->post($url, [
                'fields' => $this->mapToFirestoreFields($data),
            ] + ($documentId !== '' ? ['documentId' => $documentId] : []));

        if ($response->failed()) {
            throw new RequestException($response);
        }

        return $response->json();
    }

    private function loadServiceAccount(string $configPath): array
    {
        if (trim($configPath) === '') {
            throw new InvalidArgumentException('Firebase config path is required.');
        }

        $resolvedPath = str_starts_with($configPath, '/') || preg_match('/^[A-Za-z]:\\/', $configPath)
            ? $configPath
            : base_path($configPath);

        if (! is_file($resolvedPath)) {
            throw new InvalidArgumentException("Firebase config file not found: {$resolvedPath}");
        }

        $content = file_get_contents($resolvedPath);
        $decoded = json_decode($content ?: '{}', true);

        if (! is_array($decoded)) {
            throw new InvalidArgumentException('Invalid Firebase config JSON.');
        }

        foreach (['project_id', 'client_email', 'private_key'] as $requiredKey) {
            if (empty($decoded[$requiredKey])) {
                throw new InvalidArgumentException("Missing '{$requiredKey}' in Firebase config.");
            }
        }

        return $decoded;
    }

    private function issueAccessToken(array $serviceAccount): string
    {
        $now = time();
        $jwtHeader = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtPayload = $this->base64UrlEncode(json_encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => $serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $unsignedToken = "{$jwtHeader}.{$jwtPayload}";
        $signature = '';
        $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);

        if (! $privateKey || ! openssl_sign($unsignedToken, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new InvalidArgumentException('Unable to sign Firebase JWT with provided private key.');
        }

        $jwt = $unsignedToken.'.'.$this->base64UrlEncode($signature);

        $response = Http::asForm()->post($serviceAccount['token_uri'] ?? 'https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);

        if ($response->failed()) {
            throw new RequestException($response);
        }

        $accessToken = (string) $response->json('access_token', '');

        if ($accessToken === '') {
            throw new InvalidArgumentException('Unable to obtain Firebase access token.');
        }

        return $accessToken;
    }

    private function mapToFirestoreFields(array $data): array
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[(string) $key] = $this->toFirestoreValue($value);
        }

        return $fields;
    }

    private function toFirestoreValue(mixed $value): array
    {
        if (is_string($value)) {
            return ['stringValue' => $value];
        }

        if (is_int($value)) {
            return ['integerValue' => (string) $value];
        }

        if (is_float($value)) {
            return ['doubleValue' => $value];
        }

        if (is_bool($value)) {
            return ['booleanValue' => $value];
        }

        if ($value === null) {
            return ['nullValue' => null];
        }

        if (is_array($value)) {
            if (array_is_list($value)) {
                return [
                    'arrayValue' => [
                        'values' => array_map(fn (mixed $item) => $this->toFirestoreValue($item), $value),
                    ],
                ];
            }

            return [
                'mapValue' => [
                    'fields' => $this->mapToFirestoreFields($value),
                ],
            ];
        }

        return ['stringValue' => (string) $value];
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
