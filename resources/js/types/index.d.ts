import { Config } from 'ziggy-js';
import { SeoData, SiteSettings } from './seo';

export interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at?: string;
}

export interface FlashMessages {
    success?: string;
    error?: string;
    warning?: string;
    info?: string;
}

export type PageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    auth: {
        user: User;
    };
    ziggy: Config & { location: string };
    siteSettings: SiteSettings;
    seo: SeoData;
    flash: FlashMessages;
};
