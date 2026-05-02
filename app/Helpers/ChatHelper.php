<?php

namespace App\Helpers;

class ChatHelper
{
    /**
     * Convert URLs in text to clickable links
     */
    public static function linkify(string $text): string
    {
        // Pattern untuk detect URL
        $pattern = '/(https?:\/\/[^\s<>"]+|www\.[^\s<>"]+)/i';

        return preg_replace_callback($pattern, function ($matches) {
            $url = $matches[0];

            // Tambahkan https:// jika dimulai dengan www.
            $href = (stripos($url, 'www.') === 0) ? 'https://' . $url : $url;

            // Potong URL yang terlalu panjang untuk display
            $display = strlen($url) > 50 ? substr($url, 0, 47) . '...' : $url;

            return sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer" class="underline hover:no-underline font-medium break-all">%s</a>',
                htmlspecialchars($href, ENT_QUOTES, 'UTF-8'),
                htmlspecialchars($display, ENT_QUOTES, 'UTF-8')
            );
        }, $text);
    }
}
