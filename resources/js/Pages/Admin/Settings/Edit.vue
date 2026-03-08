<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import FormSection from '@/Components/Admin/FormSection.vue';
import ColorPicker from '@/Components/Admin/ColorPicker.vue';
import StripeSettings from '@/Components/Admin/Settings/StripeSettings.vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import OgImageCreator from '@/Components/Admin/OgImageCreator/OgImageCreator.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import axios from 'axios';

interface Theme {
    name: string;
    primary_color: string;
    secondary_color: string;
    accent_color: string;
    background_color: string;
    text_color: string;
    success_color: string;
    warning_color: string;
    error_color: string;
    info_color: string;
    surface_color: string;
    border_color: string;
    muted_color: string;
}

interface Font {
    name: string;
    category: string;
    google: boolean;
    weights?: string;
    value?: string;
}

interface SiteSettingsData {
    id: number;
    site_name: string;
    tagline: string | null;
    logo_path: string | null;
    show_site_name_in_nav: boolean;
    nav_logo_height: number;
    favicon_path: string | null;
    theme: string;
    primary_color: string;
    secondary_color: string;
    accent_color: string;
    background_color: string;
    text_color: string;
    success_color: string;
    warning_color: string;
    error_color: string;
    info_color: string;
    surface_color: string;
    border_color: string;
    muted_color: string;
    heading_font: string | null;
    body_font: string | null;
    contact_email: string | null;
    contact_phone: string | null;
    contact_address: string | null;
    social_links: Record<string, string> | null;
    default_meta_description: string | null;
    default_meta_keywords: string | null;
    og_default_image: string | null;
    twitter_handle: string | null;
    twitter_card_type: string | null;
    organization_name: string | null;
    organization_logo: string | null;
    organization_type: string | null;
    same_as: string[] | null;
    llms_txt_content: string | null;
    llms_crawl_delay: number | null;
    llms_allow_training: boolean;
    robots_txt_content: string | null;
    sitemap_enabled: boolean;
    geo_enabled: boolean;
    head_scripts: string | null;
    body_scripts: string | null;
    stripe_publishable_key: string | null;
    stripe_test_mode: boolean;
}

interface StripeSettingsData {
    publishable_key: string | null;
    secret_key_set: boolean;
    webhook_secret_set: boolean;
    test_mode: boolean;
}

interface Props {
    settings: SiteSettingsData;
    themes: Record<string, Theme>;
    fonts: Record<string, Font>;
    organizationTypes: Record<string, string>;
    stripeEnabled?: boolean;
    stripeSettings?: StripeSettingsData;
}

const props = defineProps<Props>();

type TabKey = 'general' | 'branding' | 'contact' | 'social' | 'seo' | 'payments' | 'advanced';

const activeTab = ref<TabKey>('general');
const showOgCreator = ref(false);

const form = useForm({
    site_name: props.settings.site_name || '',
    tagline: props.settings.tagline || '',
    logo_path: props.settings.logo_path || '',
    show_site_name_in_nav: props.settings.show_site_name_in_nav ?? true,
    nav_logo_height: props.settings.nav_logo_height ?? 32,
    favicon_path: props.settings.favicon_path || '',
    theme: props.settings.theme || 'default',
    primary_color: props.settings.primary_color || '#3B82F6',
    secondary_color: props.settings.secondary_color || '#10B981',
    accent_color: props.settings.accent_color || '#F59E0B',
    background_color: props.settings.background_color || '#FFFFFF',
    text_color: props.settings.text_color || '#1F2937',
    success_color: props.settings.success_color || '#10B981',
    warning_color: props.settings.warning_color || '#F59E0B',
    error_color: props.settings.error_color || '#EF4444',
    info_color: props.settings.info_color || '#3B82F6',
    surface_color: props.settings.surface_color || '#FFFFFF',
    border_color: props.settings.border_color || '#E5E7EB',
    muted_color: props.settings.muted_color || '#6B7280',
    heading_font: props.settings.heading_font || '',
    body_font: props.settings.body_font || '',
    contact_email: props.settings.contact_email || '',
    contact_phone: props.settings.contact_phone || '',
    contact_address: props.settings.contact_address || '',
    social_links: props.settings.social_links || {
        facebook: '',
        twitter: '',
        instagram: '',
        linkedin: '',
        youtube: '',
        tiktok: '',
    },
    default_meta_description: props.settings.default_meta_description || '',
    default_meta_keywords: props.settings.default_meta_keywords || '',
    og_default_image: props.settings.og_default_image || '',
    twitter_handle: props.settings.twitter_handle || '',
    twitter_card_type: props.settings.twitter_card_type || 'summary_large_image',
    organization_name: props.settings.organization_name || '',
    organization_logo: props.settings.organization_logo || '',
    organization_type: props.settings.organization_type || 'Organization',
    same_as: props.settings.same_as || [],
    llms_txt_content: props.settings.llms_txt_content || '',
    llms_crawl_delay: props.settings.llms_crawl_delay || 0,
    llms_allow_training: props.settings.llms_allow_training,
    robots_txt_content: props.settings.robots_txt_content || '',
    sitemap_enabled: props.settings.sitemap_enabled,
    geo_enabled: props.settings.geo_enabled,
    head_scripts: props.settings.head_scripts || '',
    body_scripts: props.settings.body_scripts || '',
    stripe_publishable_key: props.settings.stripe_publishable_key || '',
    stripe_secret_key: '',
    stripe_webhook_secret: '',
    stripe_test_mode: props.settings.stripe_test_mode ?? true,
});

const applyTheme = (themeName: string) => {
    const theme = props.themes[themeName];
    if (theme) {
        form.theme = themeName;
        form.primary_color = theme.primary_color;
        form.secondary_color = theme.secondary_color;
        form.accent_color = theme.accent_color;
        form.background_color = theme.background_color;
        form.text_color = theme.text_color;
        form.success_color = theme.success_color;
        form.warning_color = theme.warning_color;
        form.error_color = theme.error_color;
        form.info_color = theme.info_color;
        form.surface_color = theme.surface_color;
        form.border_color = theme.border_color;
        form.muted_color = theme.muted_color;
    }
};

const submit = () => {
    form.patch(route('admin.settings.update'));
};

const addSameAs = () => {
    form.same_as.push('');
};

const removeSameAs = (index: number) => {
    form.same_as.splice(index, 1);
};

interface Tab {
    key: TabKey;
    label: string;
}

const tabs = computed((): Tab[] => {
    const allTabs: Tab[] = [
        { key: 'general', label: 'General' },
        { key: 'branding', label: 'Branding' },
        { key: 'contact', label: 'Contact' },
        { key: 'social', label: 'Social' },
        { key: 'seo', label: 'SEO' },
    ];
    if (props.stripeEnabled) {
        allTabs.push({ key: 'payments', label: 'Payments' });
    }
    allTabs.push({ key: 'advanced', label: 'Advanced' });
    return allTabs;
});

// Font helpers
const fontCategories = {
    'sans-serif': 'Sans Serif',
    'serif': 'Serif',
    'monospace': 'Monospace',
    'system': 'System',
};

const groupedFonts = computed(() => {
    const groups: Record<string, Array<{ key: string; font: Font }>> = {};
    for (const [key, font] of Object.entries(props.fonts)) {
        const category = font.category;
        if (!groups[category]) {
            groups[category] = [];
        }
        groups[category].push({ key, font });
    }
    return groups;
});

const loadedFonts = ref<Set<string>>(new Set());

const loadGoogleFont = (fontName: string) => {
    const font = props.fonts[fontName];
    if (!font || !font.google || loadedFonts.value.has(fontName)) return;

    const weights = font.weights || '400';
    const fontFamily = fontName.replace(/ /g, '+');
    const link = document.createElement('link');
    link.href = `https://fonts.googleapis.com/css2?family=${fontFamily}:wght@${weights}&display=swap`;
    link.rel = 'stylesheet';
    document.head.appendChild(link);
    loadedFonts.value.add(fontName);
};

const getFontStyle = (fontName: string): Record<string, string> => {
    const font = props.fonts[fontName];
    if (!font) return {};

    if (font.value) {
        return { fontFamily: font.value };
    }
    return { fontFamily: `"${fontName}", ${font.category}` };
};

// Load selected fonts on mount
onMounted(() => {
    if (form.heading_font) loadGoogleFont(form.heading_font);
    if (form.body_font) loadGoogleFont(form.body_font);
});

// AI Generation state
const aiGenerating = ref<Record<string, boolean>>({
    llms_txt: false,
    robots_txt: false,
    meta_description: false,
    meta_keywords: false,
});

const aiError = ref<string | null>(null);

const generateWithAi = async (field: 'llms_txt' | 'robots_txt' | 'meta_description' | 'meta_keywords') => {
    aiGenerating.value[field] = true;
    aiError.value = null;

    const endpoints: Record<string, string> = {
        llms_txt: route('admin.ai.generate-llms-txt'),
        robots_txt: route('admin.ai.generate-robots-txt'),
        meta_description: route('admin.ai.generate-meta-description'),
        meta_keywords: route('admin.ai.generate-meta-keywords'),
    };

    const formFields: Record<string, keyof typeof form> = {
        llms_txt: 'llms_txt_content',
        robots_txt: 'robots_txt_content',
        meta_description: 'default_meta_description',
        meta_keywords: 'default_meta_keywords',
    };

    try {
        const response = await axios.post(endpoints[field]);
        if (response.data.success && response.data.content) {
            (form as any)[formFields[field]] = response.data.content;
        }
    } catch (error: any) {
        aiError.value = error.response?.data?.message || 'Failed to generate content. Please try again.';
        setTimeout(() => aiError.value = null, 5000);
    } finally {
        aiGenerating.value[field] = false;
    }
};
</script>

<template>
    <Head title="Site Settings" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Site Settings
                </h2>
                <PrimaryButton @click="submit" :disabled="form.processing">
                    Save Settings
                </PrimaryButton>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-lg bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-green-700">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <!-- Tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                            <button
                                v-for="tab in tabs"
                                :key="tab.key"
                                type="button"
                                @click="activeTab = tab.key"
                                :class="[
                                    activeTab === tab.key
                                        ? 'border-indigo-500 text-gray-700'
                                        : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
                                    'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium'
                                ]"
                            >
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>

                    <form @submit.prevent="submit" class="p-6">
                        <!-- General Tab -->
                        <div v-show="activeTab === 'general'" class="space-y-6 max-w-2xl">
                            <FormSection title="Site Identity">
                                <div class="space-y-4">
                                    <div>
                                        <InputLabel for="site_name" value="Site Name" />
                                        <TextInput
                                            id="site_name"
                                            v-model="form.site_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="Your Site Name"
                                        />
                                        <InputError :message="form.errors.site_name" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="tagline" value="Tagline" />
                                        <TextInput
                                            id="tagline"
                                            v-model="form.tagline"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="A short tagline for your site"
                                        />
                                        <InputError :message="form.errors.tagline" class="mt-2" />
                                    </div>
                                </div>
                            </FormSection>

                            <FormSection title="Logo & Favicon">
                                <div class="space-y-4">
                                    <div>
                                        <InputLabel value="Logo" />
                                        <div class="mt-1">
                                            <ImageUploader v-model="form.logo_path" label="Logo" />
                                        </div>
                                        <InputError :message="form.errors.logo_path" class="mt-2" />
                                    </div>

                                    <div v-if="form.logo_path" class="space-y-4">
                                        <div class="flex items-center gap-3">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input
                                                    type="checkbox"
                                                    v-model="form.show_site_name_in_nav"
                                                    class="sr-only peer"
                                                />
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                            </label>
                                            <span class="text-sm text-gray-700">Show site name in navigation</span>
                                        </div>

                                        <div>
                                            <InputLabel value="Logo Size in Navigation" />
                                            <div class="mt-2 flex items-center gap-4">
                                                <input
                                                    type="range"
                                                    v-model.number="form.nav_logo_height"
                                                    min="24"
                                                    max="80"
                                                    step="4"
                                                    class="w-48 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                                />
                                                <span class="text-sm text-gray-600 w-16">{{ form.nav_logo_height }}px</span>
                                                <img
                                                    :src="form.logo_path"
                                                    alt="Logo preview"
                                                    :style="{ height: form.nav_logo_height + 'px' }"
                                                    class="w-auto border border-gray-200 rounded"
                                                />
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <InputLabel value="Favicon" />
                                        <p class="text-xs text-gray-500 mb-2">Recommended: 32x32 or 64x64 pixels, PNG or ICO format</p>
                                        <div class="mt-1">
                                            <ImageUploader v-model="form.favicon_path" label="Favicon" accept="image/*,.ico" />
                                        </div>
                                        <InputError :message="form.errors.favicon_path" class="mt-2" />
                                    </div>
                                </div>
                            </FormSection>
                        </div>

                        <!-- Branding Tab -->
                        <div v-show="activeTab === 'branding'" class="space-y-6 max-w-2xl">
                            <FormSection title="Theme Presets">
                                <div class="grid grid-cols-3 gap-4">
                                    <button
                                        v-for="(theme, key) in themes"
                                        :key="key"
                                        type="button"
                                        @click="applyTheme(String(key))"
                                        :class="[
                                            form.theme === key
                                                ? 'ring-2 ring-indigo-500'
                                                : 'ring-1 ring-gray-200 hover:ring-gray-300',
                                            'rounded-lg p-4 text-center transition-all'
                                        ]"
                                    >
                                        <div class="flex justify-center gap-1 mb-2">
                                            <div
                                                :style="{ backgroundColor: theme.primary_color }"
                                                class="w-4 h-4 rounded"
                                            ></div>
                                            <div
                                                :style="{ backgroundColor: theme.secondary_color }"
                                                class="w-4 h-4 rounded"
                                            ></div>
                                            <div
                                                :style="{ backgroundColor: theme.accent_color }"
                                                class="w-4 h-4 rounded"
                                            ></div>
                                        </div>
                                        <p class="text-sm font-medium text-gray-900">{{ theme.name }}</p>
                                    </button>
                                </div>
                            </FormSection>

                            <FormSection title="Brand Colors">
                                <div class="grid grid-cols-2 gap-4">
                                    <ColorPicker
                                        v-model="form.primary_color"
                                        label="Primary Color"
                                        :error="form.errors.primary_color"
                                    />
                                    <ColorPicker
                                        v-model="form.secondary_color"
                                        label="Secondary Color"
                                        :error="form.errors.secondary_color"
                                    />
                                    <ColorPicker
                                        v-model="form.accent_color"
                                        label="Accent Color"
                                        :error="form.errors.accent_color"
                                    />
                                    <ColorPicker
                                        v-model="form.background_color"
                                        label="Background Color"
                                        :error="form.errors.background_color"
                                    />
                                    <ColorPicker
                                        v-model="form.text_color"
                                        label="Text Color"
                                        :error="form.errors.text_color"
                                    />
                                </div>
                            </FormSection>

                            <FormSection title="Status Colors">
                                <div class="grid grid-cols-2 gap-4">
                                    <ColorPicker
                                        v-model="form.success_color"
                                        label="Success Color"
                                        :error="form.errors.success_color"
                                    />
                                    <ColorPicker
                                        v-model="form.warning_color"
                                        label="Warning Color"
                                        :error="form.errors.warning_color"
                                    />
                                    <ColorPicker
                                        v-model="form.error_color"
                                        label="Error Color"
                                        :error="form.errors.error_color"
                                    />
                                    <ColorPicker
                                        v-model="form.info_color"
                                        label="Info Color"
                                        :error="form.errors.info_color"
                                    />
                                </div>
                            </FormSection>

                            <FormSection title="Surface Colors">
                                <div class="grid grid-cols-2 gap-4">
                                    <ColorPicker
                                        v-model="form.surface_color"
                                        label="Surface Color"
                                        :error="form.errors.surface_color"
                                    />
                                    <ColorPicker
                                        v-model="form.border_color"
                                        label="Border Color"
                                        :error="form.errors.border_color"
                                    />
                                    <ColorPicker
                                        v-model="form.muted_color"
                                        label="Muted Color"
                                        :error="form.errors.muted_color"
                                    />
                                </div>
                            </FormSection>

                            <FormSection title="Typography">
                                <div class="space-y-4">
                                    <!-- Heading Font -->
                                    <div>
                                        <InputLabel for="heading_font" value="Heading Font" />
                                        <select
                                            id="heading_font"
                                            v-model="form.heading_font"
                                            @change="loadGoogleFont(form.heading_font)"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                        >
                                            <option value="">Select a font...</option>
                                            <optgroup v-for="(fonts, category) in groupedFonts" :key="category" :label="fontCategories[category as keyof typeof fontCategories] || category">
                                                <option
                                                    v-for="{ key, font } in fonts"
                                                    :key="key"
                                                    :value="key"
                                                    :style="getFontStyle(key)"
                                                >
                                                    {{ font.name }}
                                                </option>
                                            </optgroup>
                                        </select>
                                        <div v-if="form.heading_font" class="mt-2 p-3 bg-gray-50 rounded-md border">
                                            <p class="text-xs text-gray-500 mb-1">Preview:</p>
                                            <p class="text-2xl font-bold" :style="getFontStyle(form.heading_font)">
                                                The quick brown fox jumps over the lazy dog
                                            </p>
                                        </div>
                                        <InputError :message="form.errors.heading_font" class="mt-2" />
                                    </div>

                                    <!-- Body Font -->
                                    <div>
                                        <InputLabel for="body_font" value="Body Font" />
                                        <select
                                            id="body_font"
                                            v-model="form.body_font"
                                            @change="loadGoogleFont(form.body_font)"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                        >
                                            <option value="">Select a font...</option>
                                            <optgroup v-for="(fonts, category) in groupedFonts" :key="category" :label="fontCategories[category as keyof typeof fontCategories] || category">
                                                <option
                                                    v-for="{ key, font } in fonts"
                                                    :key="key"
                                                    :value="key"
                                                    :style="getFontStyle(key)"
                                                >
                                                    {{ font.name }}
                                                </option>
                                            </optgroup>
                                        </select>
                                        <div v-if="form.body_font" class="mt-2 p-3 bg-gray-50 rounded-md border">
                                            <p class="text-xs text-gray-500 mb-1">Preview:</p>
                                            <p class="text-base" :style="getFontStyle(form.body_font)">
                                                The quick brown fox jumps over the lazy dog. Pack my box with five dozen liquor jugs. How vexingly quick daft zebras jump!
                                            </p>
                                        </div>
                                        <InputError :message="form.errors.body_font" class="mt-2" />
                                    </div>
                                </div>
                            </FormSection>
                        </div>

                        <!-- Contact Tab -->
                        <div v-show="activeTab === 'contact'" class="space-y-6 max-w-2xl">
                            <FormSection title="Contact Information">
                                <div class="space-y-4">
                                    <div>
                                        <InputLabel for="contact_email" value="Email" />
                                        <TextInput
                                            id="contact_email"
                                            v-model="form.contact_email"
                                            type="email"
                                            class="mt-1 block w-full"
                                            placeholder="hello@example.com"
                                        />
                                        <InputError :message="form.errors.contact_email" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="contact_phone" value="Phone" />
                                        <TextInput
                                            id="contact_phone"
                                            v-model="form.contact_phone"
                                            type="tel"
                                            class="mt-1 block w-full"
                                            placeholder="+1 (555) 123-4567"
                                        />
                                        <InputError :message="form.errors.contact_phone" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="contact_address" value="Address" />
                                        <textarea
                                            id="contact_address"
                                            v-model="form.contact_address"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                            placeholder="123 Main St, City, State 12345"
                                        ></textarea>
                                        <InputError :message="form.errors.contact_address" class="mt-2" />
                                    </div>
                                </div>
                            </FormSection>
                        </div>

                        <!-- Social Tab -->
                        <div v-show="activeTab === 'social'" class="space-y-6 max-w-2xl">
                            <FormSection title="Social Media Links">
                                <div class="space-y-4">
                                    <!-- Facebook -->
                                    <div>
                                        <InputLabel for="social_facebook" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                            <span>Facebook</span>
                                        </InputLabel>
                                        <TextInput
                                            id="social_facebook"
                                            v-model="form.social_links.facebook"
                                            type="url"
                                            class="mt-1 block w-full"
                                            placeholder="https://facebook.com/yourpage"
                                        />
                                    </div>

                                    <!-- X (Twitter) -->
                                    <div>
                                        <InputLabel for="social_twitter" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                            </svg>
                                            <span>X</span>
                                        </InputLabel>
                                        <TextInput
                                            id="social_twitter"
                                            v-model="form.social_links.twitter"
                                            type="url"
                                            class="mt-1 block w-full"
                                            placeholder="https://x.com/yourhandle"
                                        />
                                    </div>

                                    <!-- Instagram -->
                                    <div>
                                        <InputLabel for="social_instagram" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#E4405F]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                            </svg>
                                            <span>Instagram</span>
                                        </InputLabel>
                                        <TextInput
                                            id="social_instagram"
                                            v-model="form.social_links.instagram"
                                            type="url"
                                            class="mt-1 block w-full"
                                            placeholder="https://instagram.com/yourhandle"
                                        />
                                    </div>

                                    <!-- LinkedIn -->
                                    <div>
                                        <InputLabel for="social_linkedin" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#0A66C2]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                            </svg>
                                            <span>LinkedIn</span>
                                        </InputLabel>
                                        <TextInput
                                            id="social_linkedin"
                                            v-model="form.social_links.linkedin"
                                            type="url"
                                            class="mt-1 block w-full"
                                            placeholder="https://linkedin.com/company/yourcompany"
                                        />
                                    </div>

                                    <!-- YouTube -->
                                    <div>
                                        <InputLabel for="social_youtube" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-[#FF0000]" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                            </svg>
                                            <span>YouTube</span>
                                        </InputLabel>
                                        <TextInput
                                            id="social_youtube"
                                            v-model="form.social_links.youtube"
                                            type="url"
                                            class="mt-1 block w-full"
                                            placeholder="https://youtube.com/@yourchannel"
                                        />
                                    </div>

                                    <!-- TikTok -->
                                    <div>
                                        <InputLabel for="social_tiktok" class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-black" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                            </svg>
                                            <span>TikTok</span>
                                        </InputLabel>
                                        <TextInput
                                            id="social_tiktok"
                                            v-model="form.social_links.tiktok"
                                            type="url"
                                            class="mt-1 block w-full"
                                            placeholder="https://tiktok.com/@yourhandle"
                                        />
                                    </div>
                                </div>
                            </FormSection>
                        </div>

                        <!-- SEO Tab -->
                        <div v-show="activeTab === 'seo'" class="space-y-6 max-w-2xl">
                            <FormSection title="Default Meta Tags">
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <InputLabel for="default_meta_description" value="Default Meta Description" />
                                            <button
                                                type="button"
                                                @click="generateWithAi('meta_description')"
                                                :disabled="aiGenerating.meta_description"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-gray-700 bg-indigo-50 rounded-md hover:bg-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                            >
                                                <svg v-if="aiGenerating.meta_description" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                </svg>
                                                {{ aiGenerating.meta_description ? 'Generating...' : 'Generate with AI' }}
                                            </button>
                                        </div>
                                        <textarea
                                            id="default_meta_description"
                                            v-model="form.default_meta_description"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                            placeholder="Default description for pages without custom meta"
                                        ></textarea>
                                        <InputError :message="form.errors.default_meta_description" class="mt-2" />
                                    </div>

                                    <div>
                                        <div class="flex items-center justify-between">
                                            <InputLabel for="default_meta_keywords" value="Default Meta Keywords" />
                                            <button
                                                type="button"
                                                @click="generateWithAi('meta_keywords')"
                                                :disabled="aiGenerating.meta_keywords"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-gray-700 bg-indigo-50 rounded-md hover:bg-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                            >
                                                <svg v-if="aiGenerating.meta_keywords" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                </svg>
                                                {{ aiGenerating.meta_keywords ? 'Generating...' : 'Generate with AI' }}
                                            </button>
                                        </div>
                                        <TextInput
                                            id="default_meta_keywords"
                                            v-model="form.default_meta_keywords"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="keyword1, keyword2, keyword3"
                                        />
                                        <InputError :message="form.errors.default_meta_keywords" class="mt-2" />
                                    </div>
                                </div>
                            </FormSection>

                            <FormSection title="Open Graph & X">
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <InputLabel for="og_default_image" value="Default OG Image" />
                                            <button
                                                type="button"
                                                @click="showOgCreator = true"
                                                class="text-sm text-gray-700 hover:text-indigo-800"
                                            >
                                                Create with Editor
                                            </button>
                                        </div>
                                        <div class="mt-1">
                                            <ImageUploader v-model="form.og_default_image" label="Default OG Image" />
                                        </div>
                                        <InputError :message="form.errors.og_default_image" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="twitter_handle" value="X Handle" />
                                        <TextInput
                                            id="twitter_handle"
                                            v-model="form.twitter_handle"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="@yourhandle"
                                        />
                                        <InputError :message="form.errors.twitter_handle" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="twitter_card_type" value="Default X Card Type" />
                                        <select
                                            id="twitter_card_type"
                                            v-model="form.twitter_card_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                        >
                                            <option value="summary">Summary</option>
                                            <option value="summary_large_image">Summary with Large Image</option>
                                        </select>
                                        <InputError :message="form.errors.twitter_card_type" class="mt-2" />
                                    </div>
                                </div>
                            </FormSection>

                            <FormSection title="Organization Schema">
                                <div class="space-y-4">
                                    <div>
                                        <InputLabel for="organization_name" value="Organization Name" />
                                        <TextInput
                                            id="organization_name"
                                            v-model="form.organization_name"
                                            type="text"
                                            class="mt-1 block w-full"
                                            placeholder="Your Organization Name"
                                        />
                                        <InputError :message="form.errors.organization_name" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="organization_type" value="Organization Type" />
                                        <select
                                            id="organization_type"
                                            v-model="form.organization_type"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                        >
                                            <option v-for="(label, value) in organizationTypes" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.organization_type" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel value="Organization Logo" />
                                        <div class="mt-1">
                                            <ImageUploader v-model="form.organization_logo" label="Organization Logo" />
                                        </div>
                                        <InputError :message="form.errors.organization_logo" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel value="Same As (Social Profiles)" />
                                        <div class="space-y-2 mt-1">
                                            <div v-for="(url, index) in form.same_as" :key="index" class="flex gap-2">
                                                <TextInput
                                                    v-model="form.same_as[index]"
                                                    type="url"
                                                    class="flex-1"
                                                    placeholder="https://..."
                                                />
                                                <button
                                                    type="button"
                                                    @click="removeSameAs(index)"
                                                    class="text-gray-400 hover:text-red-500"
                                                >
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <button
                                                type="button"
                                                @click="addSameAs"
                                                class="inline-flex items-center text-sm text-gray-700 hover:text-gray-600"
                                            >
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Add URL
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </FormSection>

                            <FormSection title="Features">
                                <div class="space-y-3">
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.sitemap_enabled"
                                            class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                        />
                                        <span class="ml-2 text-sm text-gray-600">Enable Sitemap</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.geo_enabled"
                                            class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                        />
                                        <span class="ml-2 text-sm text-gray-600">Enable GEO (Generative Engine Optimization)</span>
                                    </label>
                                </div>
                            </FormSection>
                        </div>

                        <!-- Payments Tab -->
                        <div v-show="activeTab === 'payments'" v-if="stripeEnabled && stripeSettings">
                            <StripeSettings
                                :settings="stripeSettings"
                                :stripe-publishable-key="form.stripe_publishable_key"
                                :stripe-secret-key="form.stripe_secret_key"
                                :stripe-webhook-secret="form.stripe_webhook_secret"
                                :stripe-test-mode="form.stripe_test_mode"
                                :errors="form.errors"
                                @update:stripe-publishable-key="form.stripe_publishable_key = $event"
                                @update:stripe-secret-key="form.stripe_secret_key = $event"
                                @update:stripe-webhook-secret="form.stripe_webhook_secret = $event"
                                @update:stripe-test-mode="form.stripe_test_mode = $event"
                            />
                        </div>

                        <!-- Advanced Tab -->
                        <div v-show="activeTab === 'advanced'" class="space-y-6 max-w-2xl">
                            <!-- AI Error Alert -->
                            <div v-if="aiError" class="rounded-md bg-red-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <p class="ml-3 text-sm text-red-700">{{ aiError }}</p>
                                </div>
                            </div>

                            <FormSection title="AI Crawler Settings (llms.txt)">
                                <div class="space-y-4">
                                    <div>
                                        <div class="flex items-center justify-between">
                                            <InputLabel for="llms_txt_content" value="llms.txt Content" />
                                            <button
                                                type="button"
                                                @click="generateWithAi('llms_txt')"
                                                :disabled="aiGenerating.llms_txt"
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-gray-700 bg-indigo-50 rounded-md hover:bg-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                            >
                                                <svg v-if="aiGenerating.llms_txt" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                                <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                </svg>
                                                {{ aiGenerating.llms_txt ? 'Generating...' : 'Generate with AI' }}
                                            </button>
                                        </div>
                                        <textarea
                                            id="llms_txt_content"
                                            v-model="form.llms_txt_content"
                                            rows="6"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm font-mono text-sm"
                                            placeholder="# Instructions for AI crawlers..."
                                        ></textarea>
                                        <InputError :message="form.errors.llms_txt_content" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="llms_crawl_delay" value="Crawl Delay (seconds)" />
                                        <input
                                            id="llms_crawl_delay"
                                            v-model.number="form.llms_crawl_delay"
                                            type="number"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
                                            min="0"
                                            max="3600"
                                        />
                                        <InputError :message="form.errors.llms_crawl_delay" class="mt-2" />
                                    </div>

                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.llms_allow_training"
                                            class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                                        />
                                        <span class="ml-2 text-sm text-gray-600">Allow AI model training on site content</span>
                                    </label>
                                </div>
                            </FormSection>

                            <FormSection title="robots.txt">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <InputLabel for="robots_txt_content" value="robots.txt Content" />
                                        <button
                                            type="button"
                                            @click="generateWithAi('robots_txt')"
                                            :disabled="aiGenerating.robots_txt"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-gray-700 bg-indigo-50 rounded-md hover:bg-indigo-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                                        >
                                            <svg v-if="aiGenerating.robots_txt" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <svg v-else class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                            {{ aiGenerating.robots_txt ? 'Generating...' : 'Generate with AI' }}
                                        </button>
                                    </div>
                                    <textarea
                                        id="robots_txt_content"
                                        v-model="form.robots_txt_content"
                                        rows="10"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm font-mono text-sm"
                                        placeholder="User-agent: *&#10;Allow: /"
                                    ></textarea>
                                    <InputError :message="form.errors.robots_txt_content" class="mt-2" />
                                </div>
                            </FormSection>

                            <FormSection title="Custom Scripts">
                                <div class="space-y-4">
                                    <div>
                                        <InputLabel for="head_scripts" value="Head Scripts (before </head>)" />
                                        <textarea
                                            id="head_scripts"
                                            v-model="form.head_scripts"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm font-mono text-sm"
                                            placeholder="<!-- Analytics, tracking codes, etc. -->"
                                        ></textarea>
                                        <InputError :message="form.errors.head_scripts" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="body_scripts" value="Body Scripts (before </body>)" />
                                        <textarea
                                            id="body_scripts"
                                            v-model="form.body_scripts"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm font-mono text-sm"
                                            placeholder="<!-- Chat widgets, etc. -->"
                                        ></textarea>
                                        <InputError :message="form.errors.body_scripts" class="mt-2" />
                                    </div>
                                </div>
                            </FormSection>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <OgImageCreator
            :show="showOgCreator"
            :initial-title="form.site_name"
            @close="showOgCreator = false"
            @created="(url) => { form.og_default_image = url; showOgCreator = false; }"
        />
    </AuthenticatedLayout>
</template>
