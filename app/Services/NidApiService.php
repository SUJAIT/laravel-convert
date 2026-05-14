<?php

namespace App\Services;

use App\DTOs\NidData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NidApiService
{
    private string $apiBase;
    private string $apiKey;
    private int $timeoutSeconds;

    public function __construct()
    {
        $this->apiBase = config('services.nid_api.base_url')
            ?? env('NID_API_BASE', 'http://localhost:4000');

        $this->apiKey = config('services.nid_api.key')
            ?? env('NID_API_KEY', '');

        $this->timeoutSeconds = (int) (
            config('services.nid_api.timeout')
            ?? env('NID_API_TIMEOUT', 15)
        );
    }

    public function fetch(string $nid, string $dob): NidData
    {
        $url = $this->buildUrl($nid, $dob);

        try {
            $response = Http::timeout($this->timeoutSeconds)->get($url);
        } catch (\Exception $e) {
            Log::error('NID API connection error', [
                'nid' => $nid,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('NID_API_ERROR');
        }

        if (! $response->successful()) {
            Log::warning('NID API non-200 response', [
                'nid' => $nid,
                'status' => $response->status(),
            ]);

            throw new \RuntimeException('NID_API_ERROR');
        }

        $raw = $response->json();

        if (! $this->isSuccessResponse($raw)) {
            throw new \RuntimeException('NID_NOT_FOUND');
        }

        return $this->normalise($raw);
    }

    protected function buildUrl(string $nid, string $dob): string
    {
        return rtrim($this->apiBase, '/') . '/?' . http_build_query([
            'key' => $this->apiKey,
            'nid' => $nid,
            'dob' => $dob,
        ]);
    }

    protected function isSuccessResponse(?array $raw): bool
    {
        if (empty($raw)) {
            return false;
        }

        return ($raw['Success'] ?? null) === 'True'
            || ($raw['success'] ?? null) === 'success'
            || ($raw['status'] ?? null) === 'success';
    }

    protected function normalise(array $raw): NidData
    {
        return NidData::fromApiResponse($raw);
    }
}