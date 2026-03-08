<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ProductForm from '@/Components/Admin/Products/ProductForm.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Product, ProductCategory, PageComponent, ContentBlock } from '@/types/admin';

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
    product: Product;
    categories: ProductCategory[];
    stripeConfigured: boolean;
    components: Record<string, ComponentCategory>;
    availablePages: AvailablePage[];
}

const props = withDefaults(defineProps<Props>(), {
    components: () => ({}),
    availablePages: () => [],
});

const form = useForm({
    name: props.product.name,
    slug: props.product.slug,
    description: props.product.description || '',
    price: Number(props.product.price),
    currency: props.product.currency,
    image: props.product.image || '',
    gallery_images: (props.product.gallery_images || []) as string[],
    content: (props.product.draft_content ?? props.product.content ?? []) as ContentBlock[],
    category_id: props.product.category_id,
    sync_to_stripe: props.product.sync_to_stripe,
    is_active: props.product.is_active,
    is_featured: props.product.is_featured,
    type: props.product.type,
    recurring_interval: props.product.recurring_interval || '',
    track_inventory: props.product.track_inventory,
    quantity: props.product.quantity,
    publish: false,
});

const saveDraft = () => {
    form.publish = false;
    form.put(route('admin.products.update', props.product.id));
};

const saveAndPublish = () => {
    form.publish = true;
    form.put(route('admin.products.update', props.product.id));
};

const cancel = () => {
    router.visit(route('admin.products.index'));
};

const showDeleteModal = ref(false);
const isDeleting = ref(false);

const deleteProduct = () => {
    isDeleting.value = true;
    router.delete(route('admin.products.destroy', props.product.id), {
        onFinish: () => {
            isDeleting.value = false;
            showDeleteModal.value = false;
        },
    });
};
</script>

<template>
    <Head :title="`Edit ${product.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
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
                        Edit Product
                    </h2>
                </div>
                <button
                    type="button"
                    @click="showDeleteModal = true"
                    class="inline-flex items-center text-sm text-red-600 hover:text-red-800"
                >
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-lg bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-green-700">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <!-- Stripe Sync Status -->
                <div v-if="product.sync_to_stripe && product.stripe_product_id" class="mb-6 rounded-lg bg-indigo-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-indigo-800">Synced to Stripe</h3>
                            <p class="mt-1 text-sm text-indigo-700">
                                Product ID: <code class="bg-indigo-100 px-1 py-0.5 rounded text-xs">{{ product.stripe_product_id }}</code>
                                <span v-if="product.stripe_price_id" class="ml-2">
                                    Price ID: <code class="bg-indigo-100 px-1 py-0.5 rounded text-xs">{{ product.stripe_price_id }}</code>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Meta Info -->
                <div class="mb-6 flex items-center gap-4 text-sm text-gray-500">
                    <span v-if="product.creator">Created by {{ product.creator.name }}</span>
                    <span v-if="product.updater">Last updated by {{ product.updater.name }}</span>
                    <span
                        v-if="product.has_unpublished_changes"
                        class="inline-flex items-center rounded-full bg-orange-100 text-orange-800 px-2.5 py-0.5 text-xs font-medium"
                    >
                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        Unpublished Changes
                    </span>
                </div>

                <ProductForm
                    :form="form"
                    :categories="categories"
                    :stripe-configured="stripeConfigured"
                    :components="components"
                    :available-pages="availablePages"
                    :is-editing="true"
                    submit-label="Save Draft"
                    @submit="saveDraft"
                    @cancel="cancel"
                >
                    <template #actions>
                        <div class="flex justify-end gap-3">
                            <SecondaryButton type="button" @click="cancel">
                                Cancel
                            </SecondaryButton>
                            <SecondaryButton @click="saveDraft" :disabled="form.processing">
                                Save Draft
                            </SecondaryButton>
                            <PrimaryButton @click="saveAndPublish" :disabled="form.processing">
                                Save & Publish
                            </PrimaryButton>
                        </div>
                    </template>
                </ProductForm>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" max-width="md" @close="showDeleteModal = false">
            <div class="p-6">
                <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-center text-gray-900">Delete Product</h3>
                <p class="mt-2 text-sm text-center text-gray-500">
                    Are you sure you want to delete "{{ product.name }}"? This action cannot be undone.
                </p>
                <div class="mt-6 flex justify-center gap-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancel
                    </SecondaryButton>
                    <DangerButton @click="deleteProduct" :disabled="isDeleting">
                        {{ isDeleting ? 'Deleting...' : 'Delete Product' }}
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
