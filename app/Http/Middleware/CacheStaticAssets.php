<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheStaticAssets
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $path = $request->path();

        // Apply caching and compression for static assets
        if (preg_match('/\.(css|js|woff2?|ttf|eot|svg|png|jpg|jpeg|gif|ico|webp)(\?.*)?$/i', $path)) {
            // Cache for 1 year (immutable assets with version query string)
            $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
            $response->headers->set('Vary', 'Accept-Encoding');

            // Add ETag for conditional requests
            $content = $response->getContent();
            if ($content) {
                $etag = '"' . md5($content) . '"';
                $response->headers->set('ETag', $etag);

                // Check If-None-Match header (304 Not Modified)
                $ifNoneMatch = $request->header('If-None-Match');
                if ($ifNoneMatch === $etag) {
                    return response('', 304)->withHeaders([
                        'Cache-Control' => 'public, max-age=31536000, immutable',
                        'ETag' => $etag,
                    ]);
                }

                // GZIP compression for text-based assets (CSS, JS, SVG)
                if (preg_match('/\.(css|js|svg)(\?.*)?$/i', $path)) {
                    $acceptEncoding = $request->header('Accept-Encoding', '');

                    if (str_contains($acceptEncoding, 'gzip') && function_exists('gzencode')) {
                        $compressed = gzencode($content, 6);
                        if ($compressed !== false) {
                            $response->setContent($compressed);
                            $response->headers->set('Content-Encoding', 'gzip');
                            $response->headers->set('Content-Length', strlen($compressed));
                        }
                    }
                }
            }
        }

        return $response;
    }
}
