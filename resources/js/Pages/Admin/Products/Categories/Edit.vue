<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SlugInput from '@/Components/Admin/SlugInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import ImageUploader from '@/Components/Admin/ImageUploader.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import type { ProductCategory } from '@/types/admin';

interface Props {
    category: ProductCategory;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.category.name,
    slug: props.category.slug,
    description: props.category.description || '',
    image: props.category.image || '',
    is_featured: props.category.is_featured,
    sort_order: props.category.sort_order,
});

const submit = () => {
    form.patch(route('admin.products.categories.update', props.category.id));
};
</script>

<template>
    <Head :title="`Edit: ${category.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.products.categories.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Edit Product Category
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
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

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                placeholder="Category name..."
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
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                placeholder="Optional description..."
                            ></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel value="Category Image" />
                            <ImageUploader
                                v-model="form.image"
                                label="Category image"
                            />
                            <p class="mt-1 text-sm text-gray-500">Optional image for this category.</p>
                            <InputError :message="form.errors.image" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-3">
                            <input
                                id="is_featured"
                                v-model="form.is_featured"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-gray-800 focus:ring-gray-700"
                            />
                            <InputLabel for="is_featured" value="Featured Category" class="!mb-0" />
                        </div>
                        <p class="text-sm text-gray-500 -mt-4">Featured categories may be highlighted on the storefront.</p>

                        <div>
                            <InputLabel for="sort_order" value="Sort Order" />
                            <input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                class="mt-1 block w-32 rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
                                min="0"
                            />
                            <p class="mt-1 text-sm text-gray-500">Lower numbers appear first.</p>
                            <InputError :message="form.errors.sort_order" class="mt-2" />
                        </div>

                        <div class="flex justify-end">
                            <PrimaryButton :disabled="form.processing">
                                Save Changes
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
