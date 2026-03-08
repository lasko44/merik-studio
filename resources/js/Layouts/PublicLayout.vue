<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface NavItem {
    title: string;
    path: string;
}

interface Settings {
    siteName: string;
    tagline: string | null;
    logoPath: string | null;
    showSiteNameInNav: boolean;
    navLogoHeight: number;
    primaryColor: string;
    secondaryColor: string;
    accentColor: string;
    backgroundColor: string;
    textColor: string;
    successColor: string;
    warningColor: string;
    errorColor: string;
    infoColor: string;
    surfaceColor: string;
    borderColor: string;
    mutedColor: string;
    contactEmail: string | null;
    contactPhone: string | null;
    contactAddress: string | null;
    socialLinks: Array<{ platform: string; url: string }> | null;
}

interface Props {
    settings: Settings;
    navigation: NavItem[];
}

const props = defineProps<Props>();

const cssVars = computed(() => ({
    '--color-primary': props.settings.primaryColor,
    '--color-secondary': props.settings.secondaryColor,
    '--color-accent': props.settings.accentColor,
    '--color-background': props.settings.backgroundColor,
    '--color-text': props.settings.textColor,
    '--color-success': props.settings.successColor,
    '--color-warning': props.settings.warningColor,
    '--color-error': props.settings.errorColor,
    '--color-info': props.settings.infoColor,
    '--color-surface': props.settings.surfaceColor,
    '--color-border': props.settings.borderColor,
    '--color-muted': props.settings.mutedColor,
}));

// Helper to darken a hex color for footer background
const darkenColor = (hex: string, percent: number): string => {
    const num = parseInt(hex.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = Math.max((num >> 16) - amt, 0);
    const G = Math.max((num >> 8 & 0x00FF) - amt, 0);
    const B = Math.max((num & 0x0000FF) - amt, 0);
    return `#${(0x1000000 + R * 0x10000 + G * 0x100 + B).toString(16).slice(1)}`;
};

const footerBgColor = computed(() => darkenColor(props.settings.primaryColor, 70));

const currentYear = new Date().getFullYear();
</script>

<template>
    <div class="min-h-screen flex flex-col" :style="cssVars">
        <!-- Skip Link for Accessibility -->
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-indigo-600 focus:text-white focus:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
        >
            Skip to main content
        </a>

        <!-- Header -->
        <header class="bg-white shadow-sm sticky top-0 z-50" role="banner">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-3">
                        <img
                            v-if="settings.logoPath"
                            :src="settings.logoPath"
                            :alt="settings.siteName"
                            class="w-auto"
                            :style="{ height: (settings.navLogoHeight || 32) + 'px' }"
                        />
                        <span
                            v-if="settings.showSiteNameInNav || !settings.logoPath"
                            class="text-xl font-bold text-gray-900"
                        >
                            {{ settings.siteName }}
                        </span>
                    </Link>

                    <!-- Navigation -->
                    <nav class="hidden md:flex items-center gap-8" aria-label="Main navigation">
                        <Link
                            href="/"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            Home
                        </Link>
                        <Link
                            v-for="item in navigation"
                            :key="item.path"
                            :href="item.path"
                            class="text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            {{ item.title }}
                        </Link>
                    </nav>

                    <!-- CTA Button -->
                    <Link
                        href="/contact"
                        class="hidden sm:inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors"
                        :style="{ backgroundColor: settings.primaryColor }"
                    >
                        Contact Us
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main id="main-content" class="flex-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="text-white" :style="{ backgroundColor: footerBgColor }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand -->
                    <div class="col-span-1 md:col-span-2">
                        <h3 class="text-xl font-bold mb-2">{{ settings.siteName }}</h3>
                        <p v-if="settings.tagline" class="text-white/70 mb-4">
                            {{ settings.tagline }}
                        </p>
                        <div v-if="settings.socialLinks" class="flex gap-4">
                            <a
                                v-for="link in settings.socialLinks"
                                :key="link.platform"
                                :href="link.url"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="text-white/70 hover:text-white transition-colors"
                            >
                                {{ link.platform }}
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">Quick Links</h4>
                        <ul class="space-y-2">
                            <li>
                                <Link href="/" class="text-white/70 hover:text-white transition-colors">Home</Link>
                            </li>
                            <li v-for="item in navigation.slice(0, 4)" :key="item.path">
                                <Link :href="item.path" class="text-white/70 hover:text-white transition-colors">
                                    {{ item.title }}
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- Contact -->
                    <div>
                        <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">Contact</h4>
                        <ul class="space-y-2 text-white/70">
                            <li v-if="settings.contactEmail">
                                <a :href="'mailto:' + settings.contactEmail" class="hover:text-white transition-colors">
                                    {{ settings.contactEmail }}
                                </a>
                            </li>
                            <li v-if="settings.contactPhone">
                                <a :href="'tel:' + settings.contactPhone" class="hover:text-white transition-colors">
                                    {{ settings.contactPhone }}
                                </a>
                            </li>
                            <li v-if="settings.contactAddress" class="text-sm">
                                {{ settings.contactAddress }}
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-white/20 mt-8 pt-8 text-center text-white/70 text-sm">
                    <p>&copy; {{ currentYear }} {{ settings.siteName }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
a:hover {
    color: var(--color-primary);
}
</style>
