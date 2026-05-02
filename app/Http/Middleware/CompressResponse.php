<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompressResponse
{
    /**
     * Handle an incoming request and compress the response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip if already encoded or not compressible
        if ($this->shouldSkipCompression($request, $response)) {
            return $response;
        }

        $acceptEncoding = $request->header('Accept-Encoding', '');

        if (str_contains($acceptEncoding, 'gzip') && function_exists('gzencode')) {
            return $this->compressWithGzip($response);
        }

        return $response;
    }

    /**
     * Check if compression should be skipped.
     */
    protected function shouldSkipCompression(Request $request, Response $response): bool
    {
        // Skip for streaming/binary responses
        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            return true;
        }

        // Skip if already compressed
        if ($response->headers->has('Content-Encoding')) {
            return true;
        }

        // Skip for non-compressible content types
        $contentType = $response->headers->get('Content-Type', '');
        $compressibleTypes = [
            'text/html',
            'text/plain',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'image/svg+xml',
        ];

        $isCompressible = false;
        foreach ($compressibleTypes as $type) {
            if (str_contains($contentType, $type)) {
                $isCompressible = true;
                break;
            }
        }

        if (!$isCompressible) {
            return true;
        }

        // Skip small responses (< 1KB, not worth compressing)
        $content = $response->getContent();
        if ($content === false || strlen($content) < 1024) {
            return true;
        }

        return false;
    }

    /**
     * Compress response with GZIP.
     */
    protected function compressWithGzip(Response $response): Response
    {
        $content = $response->getContent();

        if ($content === false) {
            return $response;
        }

        $compressed = gzencode($content, 6);

        if ($compressed === false) {
            return $response;
        }

        // Only use compression if it actually reduces size
        if (strlen($compressed) >= strlen($content)) {
            return $response;
        }

        $response->setContent($compressed);
        $response->headers->set('Content-Encoding', 'gzip');
        $response->headers->set('Content-Length', strlen($compressed));
        $response->headers->set('Vary', 'Accept-Encoding');

        return $response;
    }
}
