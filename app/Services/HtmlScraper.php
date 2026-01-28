<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

class HtmlScraper
{
    private const DEFAULT_HEADERS = [
        'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language' => 'en-US,en;q=0.5',
    ];

    private int $timeout;

    public function __construct(int $timeout = 15)
    {
        $this->timeout = $timeout;
    }

    /**
     * Fetch HTML content from a URL.
     *
     * @param string $url
     * @return array{html: string|null, error: string|null}
     */
    public function getHtml(string $url): array
    {
        try {
            $response = Http::withHeaders(self::DEFAULT_HEADERS)
                ->timeout($this->timeout)
                ->get($url);

            if ($response->failed()) {
                return [
                    'html' => null,
                    'error' => "HTTP error: {$response->status()}",
                ];
            }

            return [
                'html' => $response->body(),
                'error' => null,
            ];
        } catch (ConnectionException $e) {
            return [
                'html' => null,
                'error' => 'Failed to connect to the server',
            ];
        } catch (\Exception $e) {
            return [
                'html' => null,
                'error' => "Error fetching page: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Fetch HTML content from a URL asynchronously using Laravel's HTTP pool.
     *
     * @param array<string> $urls
     * @return array<string, array{html: string|null, error: string|null}>
     */
    public function getHtmlAsync(array $urls): array
    {
        $responses = Http::pool(function ($pool) use ($urls) {
            foreach ($urls as $key => $url) {
                $pool->as($key)
                    ->withHeaders(self::DEFAULT_HEADERS)
                    ->timeout($this->timeout)
                    ->get($url);
            }
        });

        $results = [];
        foreach ($urls as $key => $url) {
            $response = $responses[$key] ?? null;

            if ($response === null) {
                $results[$url] = [
                    'html' => null,
                    'error' => 'Request failed',
                ];
            } elseif ($response instanceof \Exception) {
                $results[$url] = [
                    'html' => null,
                    'error' => $response->getMessage(),
                ];
            } elseif ($response->failed()) {
                $results[$url] = [
                    'html' => null,
                    'error' => "HTTP error: {$response->status()}",
                ];
            } else {
                $results[$url] = [
                    'html' => $response->body(),
                    'error' => null,
                ];
            }
        }

        return $results;
    }
}
