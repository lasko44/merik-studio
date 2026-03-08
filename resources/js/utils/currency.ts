/**
 * Currency symbols mapping.
 */
export const CURRENCY_SYMBOLS: Record<string, string> = {
    'USD': '$',
    'EUR': '€',
    'GBP': '£',
    'JPY': '¥',
    'CAD': 'C$',
    'AUD': 'A$',
    'CHF': 'CHF',
    'CNY': '¥',
    'INR': '₹',
    'MXN': 'MX$',
    'BRL': 'R$',
};

/**
 * Format a price with currency symbol.
 */
export function formatPrice(price: number, currency: string = 'USD'): string {
    const symbol = CURRENCY_SYMBOLS[currency] ?? currency + ' ';
    return symbol + Number(price).toFixed(2);
}

/**
 * Format a price using Intl.NumberFormat for proper locale handling.
 */
export function formatPriceLocale(
    price: number,
    currency: string = 'USD',
    locale: string = 'en-US'
): string {
    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency,
    }).format(price);
}
