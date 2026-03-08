/**
 * Check if a URL is external (contains protocol).
 */
export function isExternalLink(url: string | null | undefined): boolean {
    if (!url) return false;
    return url.includes('://');
}

/**
 * Check if a URL is an anchor link.
 */
export function isAnchorLink(url: string | null | undefined): boolean {
    if (!url) return false;
    return url.startsWith('#');
}

/**
 * Normalize a URL path (ensure leading slash, remove trailing slash).
 */
export function normalizePath(path: string): string {
    let normalized = path.trim();
    if (!normalized.startsWith('/') && !isExternalLink(normalized)) {
        normalized = '/' + normalized;
    }
    if (normalized.length > 1 && normalized.endsWith('/')) {
        normalized = normalized.slice(0, -1);
    }
    return normalized;
}

/**
 * Get external link attributes for anchor tags.
 */
export function getExternalLinkAttrs(): Record<string, string> {
    return {
        target: '_blank',
        rel: 'noopener noreferrer',
    };
}
