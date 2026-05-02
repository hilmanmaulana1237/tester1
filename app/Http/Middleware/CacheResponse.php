<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Pages that should be cached (for guests only)
     */
    protected array $cacheableRoutes = [
        'home',
        'login',
        'register',
    ];

    /**
     * Cache duration in seconds
     */
    protected int $cacheDuration = 300; // 5 minutes

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only cache GET requests
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // Don't cache for authenticated users (dynamic content)
        if (auth()->check()) {
            $response = $next($request);
            // Add cache headers for browser caching (short duration for authenticated)
            $response->headers->set('Cache-Control', 'private, max-age=0, must-revalidate');
            return $response;
        }

        // Don't cache Livewire requests
        if ($request->hasHeader('X-Livewire')) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        // Check if this route should be cached
        if (!in_array($routeName, $this->cacheableRoutes)) {
            $response = $next($request);
            // Still add browser cache headers
            $this->addBrowserCacheHeaders($response, 60); // 1 minute
            return $response;
        }

        // Generate cache key
        $cacheKey = 'page_cache:' . md5($request->fullUrl());

        // Try to get from cache
        $cachedResponse = Cache::get($cacheKey);

        if ($cachedResponse) {
            $response = response($cachedResponse['content'])
                ->withHeaders($cachedResponse['headers']);
            $response->headers->set('X-Cache', 'HIT');
            return $response;
        }

        // Generate response
        $response = $next($request);

        // Only cache successful HTML responses
        if ($response->isSuccessful() && $this->isHtmlResponse($response)) {
            $content = $response->getContent();
            $headers = [
                'Content-Type' => $response->headers->get('Content-Type'),
            ];

            Cache::put($cacheKey, [
                'content' => $content,
                'headers' => $headers,
            ], $this->cacheDuration);

            $response->headers->set('X-Cache', 'MISS');
        }

        $this->addBrowserCacheHeaders($response, $this->cacheDuration);

        return $response;
    }

    /**
     * Check if response is HTML
     */
    protected function isHtmlResponse(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');
        return str_contains($contentType, 'text/html');
    }

    /**
     * Add browser cache headers
     */
    protected function addBrowserCacheHeaders(Response $response, int $maxAge): void
    {
        if (!$response->headers->has('Cache-Control')) {
            $response->headers->set('Cache-Control', "public, max-age={$maxAge}, s-maxage={$maxAge}");
        }

        // Add ETag for conditional requests
        $content = $response->getContent();
        if ($content && !$response->headers->has('ETag')) {
            $etag = '"' . md5($content) . '"';
            $response->headers->set('ETag', $etag);
        }
    }
}
