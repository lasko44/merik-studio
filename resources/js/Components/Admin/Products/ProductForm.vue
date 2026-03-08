<script setup lang="ts">
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import SlugInput from '@/Components/Admin/SlugInput.vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import MultiImageUploader from '@/Components/Admin/MultiImageUploader.vue';
import BlockEditor from '@/Components/PageBuilder/BlockEditor.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { computed, watch } from 'vue';
import type { ProductFormData, ProductCategory, PageComponent, ContentBlock } from '@/types/admin';

interface ComponentCategory {
    label: string;
    components: PageComponent[];
}

interface AvailablePage {
    id: number;
    title: string;
    path: string;
}

interface Props {
    form: {
        name: string;
        slug: string;
        description: string;
        price: number;
        currency: string;
        image: string;
        gallery_images: string[];
        content: ContentBlock[];
        category_id: number | null;
        sync_to_stripe: boolean;
        is_active: boolean;
        is_featured: boolean;
        type: 'one_time' | 'recurring';
        recurring_interval: string;
        track_inventory: boolean;
        quantity: number | null;
        processing: boolean;
        errors: Partial<Record<keyof ProductFormData | 'content', string>>;
    };
    categories: ProductCategory[];
    stripeConfigured: boolean;
    components?: Record<string, ComponentCategory>;
    availablePages?: AvailablePage[];
    isEditing?: boolean;
    submitLabel?: string;
}

const props = withDefaults(defineProps<Props>(), {
    categories: () => [],
    components: () => ({}),
    availablePages: () => [],
    isEditing: false,
    submitLabel: 'Create Product',
});

const emit = defineEmits<{
    submit: [];
    cancel: [];
}>();

const currencies = [
    { value: 'USD', label: 'USD ($)' },
    { value: 'EUR', label: 'EUR (\u20AC)' },
    { value: 'GBP', label: 'GBP (\u00A3)' },
    { value: 'CAD', label: 'CAD ($)' },
    { value: 'AUD', label: 'AUD ($)' },
];

const recurringIntervals = [
    { value: 'month', label: 'Monthly' },
    { value: 'year', label: 'Yearly' },
];

const showRecurringOptions = computed(() => props.form.type === 'recurring');
const showInventoryOptions = computed(() => props.form.track_inventory);

watch(() => props.form.type, (newType) => {
    if (newType === 'one_time') {
        props.form.recurring_interval = '';
    }
});

watch(() => props.form.track_inventory, (tracking) => {
    if (!tracking) {
        props.form.quantity = null;
    }
});
</script>

<template>
    <form @submit.prevent="$emit('submit')" class="space-y-6">
        <!-- Basic Information -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>

            <div class="space-y-4">
                <div>
                    <InputLabel for="name" value="Product Name" />
                    <TextInput
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="mt-1 block w-full"
                        placeholder="Enter product name..."
                        autofocus
                    />
                    <InputError :message="form.errors.name" class="mt-2" />
                </div>

                <SlugInput
                    v-model="form.slug"
                    :source="form.name"
                    label="Slug"
                    :error="form.errors.slug"
                />

                <div>
                    <InputLabel for="description" value="Description" />
                    <textarea
                        id="description"
                        v-model="form.description"
                        rows="4"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        placeholder="Describe your product..."
                    ></textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                </div>

                <div v-if="categories.length > 0">
                    <InputLabel for="category_id" value="Category" />
                    <select
                        id="category_id"
                        v-model="form.category_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                    >
                        <option :value="null">No category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                    <InputError :message="form.errors.category_id" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Pricing -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing</h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <InputLabel for="price" value="Price" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input
                            id="price"
                            v-model.number="form.price"
                            type="number"
                            step="0.01"
                            min="0"
                            class="block w-full pl-7 pr-12 rounded-md border-gray-300 focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                            placeholder="0.00"
                        />
                    </div>
                    <InputError :message="form.errors.price" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="currency" value="Currency" />
                    <select
                        id="currency"
                        v-model="form.currency"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                    >
                        <option v-for="currency in currencies" :key="currency.value" :value="currency.value">
                            {{ currency.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.currency" class="mt-2" />
                </div>
            </div>

            <div class="mt-4">
                <InputLabel value="Product Type" />
                <div class="mt-2 flex gap-4">
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            v-model="form.type"
                            value="one_time"
                            class="rounded-full border-gray-300 text-gray-800 focus:ring-gray-700"
                        />
                        <span class="ml-2 text-sm text-gray-700">One-time payment</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input
                            type="radio"
                            v-model="form.type"
                            value="recurring"
                            class="rounded-full border-gray-300 text-gray-800 focus:ring-gray-700"
                        />
                        <span class="ml-2 text-sm text-gray-700">Recurring subscription</span>
                    </label>
                </div>
            </div>

            <div v-if="showRecurringOptions" class="mt-4">
                <InputLabel for="recurring_interval" value="Billing Interval" />
                <select
                    id="recurring_interval"
                    v-model="form.recurring_interval"
                    class="mt-1 block w-48 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                >
                    <option value="">Select interval...</option>
                    <option v-for="interval in recurringIntervals" :key="interval.value" :value="interval.value">
                        {{ interval.label }}
                    </option>
                </select>
                <InputError :message="form.errors.recurring_interval" class="mt-2" />
            </div>
        </div>

        <!-- Images -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Product Images</h3>

            <div class="space-y-6">
                <div>
                    <InputLabel value="Main Image" class="mb-2" />
                    <p class="text-sm text-gray-500 mb-3">This is the primary image shown in product listings.</p>
                    <ImageUploader v-model="form.image" label="Main product image" />
                    <InputError :message="form.errors.image" class="mt-2" />
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <InputLabel value="Gallery Images" class="mb-2" />
                    <p class="text-sm text-gray-500 mb-3">Additional images shown in the product gallery. The main image will be displayed first.</p>
                    <MultiImageUploader v-model="form.gallery_images" label="Gallery image" :max-images="10" />
                    <InputError :message="form.errors.gallery_images" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Inventory -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Inventory</h3>

            <div>
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.track_inventory"
                        class="rounded border-gray-300 text-gray-800 focus:ring-gray-700"
                    />
                    <span class="ml-2 text-sm text-gray-700">Track inventory for this product</span>
                </label>
            </div>

            <div v-if="showInventoryOptions" class="mt-4">
                <InputLabel for="quantity" value="Stock Quantity" />
                <input
                    id="quantity"
                    v-model.number="form.quantity"
                    type="number"
                    min="0"
                    class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                />
                <InputError :message="form.errors.quantity" class="mt-2" />
            </div>
        </div>

        <!-- Product Page Content -->
        <div v-if="Object.keys(components).length > 0" class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Product Page Content</h3>
            <p class="text-sm text-gray-500 mb-4">
                Add custom content blocks to your product page. These will appear below the main product information.
            </p>
            <BlockEditor
                v-model="form.content"
                :components="components"
                :available-pages="availablePages"
            />
            <InputError :message="form.errors.content" class="mt-2" />
        </div>

        <!-- Status & Stripe -->
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>

            <div class="space-y-4">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.is_active"
                        class="rounded border-gray-300 text-gray-800 focus:ring-gray-700"
                    />
                    <span class="ml-2 text-sm text-gray-700">Product is active and available for purchase</span>
                </label>

                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        v-model="form.is_featured"
                        class="rounded border-gray-300 text-gray-800 focus:ring-gray-700"
                    />
                    <span class="ml-2 text-sm text-gray-700">Feature this product on the storefront</span>
                </label>

                <div class="border-t border-gray-200 pt-4">
                    <label class="inline-flex items-center" :class="{ 'opacity-50': !stripeConfigured }">
                        <input
                            type="checkbox"
                            v-model="form.sync_to_stripe"
                            :disabled="!stripeConfigured"
                            class="rounded border-gray-300 text-gray-800 focus:ring-gray-700 disabled:opacity-50"
                        />
                        <span class="ml-2 text-sm text-gray-700">Sync to Stripe for payment processing</span>
                    </label>

                    <p v-if="!stripeConfigured" class="mt-2 text-sm text-yellow-600">
                        Stripe is not configured. Configure your API keys in Settings to enable this option.
                    </p>

                    <p v-else-if="form.sync_to_stripe" class="mt-2 text-sm text-gray-500">
                        This product will be automatically synced to your Stripe account. Price changes will create new Stripe prices.
                    </p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <slot name="actions">
            <div class="flex justify-end gap-3">
                <SecondaryButton type="button" @click="$emit('cancel')">
                    Cancel
                </SecondaryButton>
                <PrimaryButton :disabled="form.processing">
                    {{ submitLabel }}
                </PrimaryButton>
            </div>
        </slot>
    </form>
</template>
