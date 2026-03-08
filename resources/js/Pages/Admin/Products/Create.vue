<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductForm from '@/Components/Admin/Products/ProductForm.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { watch } from 'vue';

import type { ProductCategory, PageComponent, ContentBlock } from '@/types/admin';

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
    stripeConfigured: boolean;
    categories: ProductCategory[];
    components: Record<string, ComponentCategory>;
    availablePages: AvailablePage[];
}

const props = withDefaults(defineProps<Props>(), {
    components: () => ({}),
    availablePages: () => [],
});

const form = useForm({
    name: '',
    slug: '',
    description: '',
    price: 0,
    currency: 'USD',
    image: '',
    gallery_images: [] as string[],
    content: [] as ContentBlock[],
    category_id: null as number | null,
    sync_to_stripe: false,
    is_active: true,
    is_featured: false,
    type: 'one_time' as 'one_time' | 'recurring',
    recurring_interval: '',
    track_inventory: false,
    quantity: null as number | null,
});

const slugify = (text: string): string => {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .trim();
};

watch(() => form.name, (newName) => {
    if (!form.slug || form.slug === slugify(form.name.slice(0, -1))) {
        form.slug = slugify(newName);
    }
});

const submit = () => {
    form.post(route('admin.products.store'));
};

const cancel = () => {
    router.visit(route('admin.products.index'));
};
</script>

<template>
    <Head title="Create Product" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.products.index')"
                    class="text-gray-500 hover:text-gray-700"
                    aria-label="Back to products"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Create Product
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <ProductForm
                    :form="form"
                    :categories="categories"
                    :stripe-configured="stripeConfigured"
                    :components="components"
                    :available-pages="availablePages"
                    submit-label="Create Product"
                    @submit="submit"
                    @cancel="cancel"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
