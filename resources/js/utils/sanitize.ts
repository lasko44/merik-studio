import DOMPurify from 'dompurify';

/**
 * Sanitize HTML content to prevent XSS attacks.
 * Uses DOMPurify with safe defaults.
 */
export function sanitizeHtml(html: string | null | undefined): string {
    if (!html) return '';
    return DOMPurify.sanitize(html, {
        ALLOWED_TAGS: [
            'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
            'p', 'br', 'hr',
            'ul', 'ol', 'li',
            'strong', 'em', 'b', 'i', 'u', 's',
            'a', 'span', 'div',
            'blockquote', 'pre', 'code',
            'table', 'thead', 'tbody', 'tr', 'th', 'td',
            'img',
        ],
        ALLOWED_ATTR: [
            'href', 'target', 'rel', 'class', 'style',
            'src', 'alt', 'title', 'width', 'height',
        ],
        ALLOW_DATA_ATTR: false,
    });
}

/**
 * Sanitize HTML with minimal tags (for inline content).
 */
export function sanitizeInlineHtml(html: string | null | undefined): string {
    if (!html) return '';
    return DOMPurify.sanitize(html, {
        ALLOWED_TAGS: ['strong', 'em', 'b', 'i', 'u', 'a', 'span', 'br'],
        ALLOWED_ATTR: ['href', 'target', 'rel', 'class'],
    });
}
