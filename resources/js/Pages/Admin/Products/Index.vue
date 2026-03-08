<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Product, ProductStats } from '@/types/admin';

interface ProductSettings {
    showInNav: boolean;
    navLabel: string;
    enableSearch: boolean;
    enableCategoryFilter: boolean;
}

interface Props {
    products: Product[];
    stats: ProductStats;
    stripeConfigured: boolean;
    productSettings: ProductSettings;
}

const props = defineProps<Props>();

const confirmingDeletion = ref<number | null>(null);
const processing = ref(false);
const showSettings = ref(false);

const settingsForm = useForm({
    showInNav: props.productSettings.showInNav,
    navLabel: props.productSettings.navLabel,
    enableSearch: props.productSettings.enableSearch,
    enableCategoryFilter: props.productSettings.enableCategoryFilter,
});

const saveSettings = () => {
    settingsForm.patch(route('admin.products.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            showSettings.value = false;
        },
    });
};

const deleteProduct = (productId: number) => {
    processing.value = true;
    router.delete(route('admin.products.destroy', productId), {
        preserveScroll: true,
        onFinish: () => {
            processing.value = false;
            confirmingDeletion.value = null;
        },
    });
};

const formatPrice = (price: number, currency: string): string => {
    const symbol = currency === 'USD' ? '$' : currency === 'EUR' ? '\u20AC' : currency === 'GBP' ? '\u00A3' : currency + ' ';
    return symbol + Number(price).toFixed(2);
};
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Products
                </h2>
                <div class="flex items-center gap-3">
                    <button
                        @click="showSettings = !showSettings"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M7.84 1.804A1 1 0 018.82 1h2.36a1 1 0 01.98.804l.331 1.652a6.993 6.993 0 011.929 1.115l1.598-.54a1 1 0 011.186.447l1.18 2.044a1 1 0 01-.205 1.251l-1.267 1.113a7.047 7.047 0 010 2.228l1.267 1.113a1 1 0 01.206 1.25l-1.18 2.045a1 1 0 01-1.187.447l-1.598-.54a6.993 6.993 0 01-1.929 1.115l-.33 1.652a1 1 0 01-.98.804H8.82a1 1 0 01-.98-.804l-.331-1.652a6.993 6.993 0 01-1.929-1.115l-1.598.54a1 1 0 01-1.186-.447l-1.18-2.044a1 1 0 01.205-1.251l1.267-1.113a7.047 7.047 0 010-2.228L1.821 7.773a1 1 0 01-.206-1.25l1.18-2.045a1 1 0 011.187-.447l1.598.54A6.993 6.993 0 017.51 3.456l.33-1.652zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                        </svg>
                        Settings
                    </button>
                    <Link
                        :href="route('admin.products.categories.index')"
                        class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.5 3A2.5 2.5 0 003 5.5v2.879a2.5 2.5 0 00.732 1.767l6.5 6.5a2.5 2.5 0 003.536 0l2.878-2.878a2.5 2.5 0 000-3.536l-6.5-6.5A2.5 2.5 0 008.38 3H5.5zM6 7a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        Categories
                    </Link>
                    <Link
                        :href="route('admin.products.create')"
                        class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-gray-800"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        New Product
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
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

                <!-- Settings Panel -->
                <div v-if="showSettings" class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Product Settings</h3>
                        <form @submit.prevent="saveSettings" class="space-y-4">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            v-model="settingsForm.showInNav"
                                            class="rounded border-gray-300 text-gray-800 focus:ring-gray-700"
                                        />
                                        <span class="text-sm text-gray-700">Include Products in navigation</span>
                                    </label>
                                </div>
                                <div v-if="settingsForm.showInNav">
                                    <label for="navLabel" class="block text-sm font-medium text-gray-700">Navigation Label</label>
                                    <input
                                        id="navLabel"
                                        type="text"
                                        v-model="settingsForm.navLabel"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <label class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="settingsForm.enableSearch"
                                        class="rounded border-gray-300 text-gray-800 focus:ring-gray-700"
                                    />
                                    <span class="text-sm text-gray-700">Enable product search</span>
                                </label>
                                <label class="flex items-center gap-2">
                                    <input
                                        type="checkbox"
                                        v-model="settingsForm.enableCategoryFilter"
                                        class="rounded border-gray-300 text-gray-800 focus:ring-gray-700"
                                    />
                                    <span class="text-sm text-gray-700">Enable category filter</span>
                                </label>
                            </div>
                            <div class="flex justify-end gap-3">
                                <button
                                    type="button"
                                    @click="showSettings = false"
                                    class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="settingsForm.processing"
                                    class="rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                                >
                                    Save Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Total Products</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ stats.total }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Active</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-green-600">{{ stats.active }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Synced to Stripe</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-indigo-600">{{ stats.synced_to_stripe }}</dd>
                    </div>
                    <div class="overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:p-6">
                        <dt class="truncate text-sm font-medium text-gray-500">Recurring</dt>
                        <dd class="mt-1 text-3xl font-semibold tracking-tight text-gray-900">{{ stats.recurring }}</dd>
                    </div>
                </div>

                <!-- Stripe Warning -->
                <div v-if="!stripeConfigured" class="mb-6 rounded-lg bg-yellow-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Stripe not configured</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                Configure your Stripe API keys in
                                <Link :href="route('admin.settings.edit')" class="font-medium underline">Settings</Link>
                                to enable payment processing.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Products Table -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div v-if="products.length === 0" class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-semibold text-gray-900">No products</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new product.</p>
                        <div class="mt-6">
                            <Link
                                :href="route('admin.products.create')"
                                class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                            >
                                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                                </svg>
                                New Product
                            </Link>
                        </div>
                    </div>

                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                    Product
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Category
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Price
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Type
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Status
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                    Stripe
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="product in products" :key="product.id">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">{{ product.name }}</span>
                                        <span
                                            v-if="product.is_featured"
                                            class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800"
                                        >
                                            Featured
                                        </span>
                                    </div>
                                    <div class="text-gray-500">
                                        <code class="rounded bg-gray-100 px-1.5 py-0.5 text-xs">{{ product.slug }}</code>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <span v-if="product.category">{{ product.category.name }}</span>
                                    <span v-else class="text-gray-400">-</span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-medium">
                                    {{ formatPrice(product.price, product.currency) }}
                                    <span v-if="product.type === 'recurring' && product.recurring_interval" class="text-gray-500 font-normal">
                                        / {{ product.recurring_interval }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            product.type === 'recurring'
                                                ? 'bg-purple-100 text-purple-800'
                                                : 'bg-gray-100 text-gray-800'
                                        ]"
                                    >
                                        {{ product.type === 'recurring' ? 'Recurring' : 'One-time' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
                                            product.is_active
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'
                                        ]"
                                    >
                                        {{ product.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span v-if="product.sync_to_stripe && product.stripe_product_id" class="inline-flex items-center gap-1 text-indigo-600">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                        </svg>
                                        Synced
                                    </span>
                                    <span v-else-if="product.sync_to_stripe" class="text-yellow-600">Pending</span>
                                    <span v-else class="text-gray-400">Not synced</span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('admin.products.edit', product.id)"
                                            class="text-gray-700 hover:text-gray-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            v-if="confirmingDeletion !== product.id"
                                            @click="confirmingDeletion = product.id"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                        <div v-else class="flex items-center gap-1">
                                            <button
                                                @click="deleteProduct(product.id)"
                                                :disabled="processing"
                                                class="text-red-600 hover:text-red-900 font-semibold"
                                            >
                                                Confirm
                                            </button>
                                            <button
                                                @click="confirmingDeletion = null"
                                                class="text-gray-600 hover:text-gray-900"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
