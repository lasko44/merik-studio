<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface Props {
    subscriberCount: number;
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    subject: '',
    preview_text: '',
    content: '',
});

const submit = () => {
    form.post(route('admin.campaigns.store'));
};
</script>

<template>
    <Head title="Create Campaign" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.campaigns.index')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M17 10a.75.75 0 01-.75.75H5.612l4.158 3.96a.75.75 0 11-1.04 1.08l-5.5-5.25a.75.75 0 010-1.08l5.5-5.25a.75.75 0 111.04 1.08L5.612 9.25H16.25A.75.75 0 0117 10z" clip-rule="evenodd" />
                    </svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campaign</h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <form @submit.prevent="submit" class="p-6 space-y-6">
                        <!-- Subscriber Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center gap-2">
                                <svg class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zM6 8a2 2 0 11-4 0 2 2 0 014 0zM1.49 15.326a.78.78 0 01-.358-.442 3 3 0 014.308-3.516 6.484 6.484 0 00-1.905 3.959c-.023.222-.014.442.025.654a4.97 4.97 0 01-2.07-.655zM16.44 15.98a4.97 4.97 0 002.07-.654.78.78 0 00.357-.442 3 3 0 00-4.308-3.517 6.484 6.484 0 011.907 3.96 2.32 2.32 0 01-.026.654zM18 8a2 2 0 11-4 0 2 2 0 014 0zM5.304 16.19a.844.844 0 01-.277-.71 5 5 0 019.947 0 .843.843 0 01-.277.71A6.975 6.975 0 0110 18a6.974 6.974 0 01-4.696-1.81z" />
                                </svg>
                                <span class="text-sm text-blue-800">
                                    This campaign will be sent to <strong>{{ subscriberCount }}</strong> active subscribers
                                </span>
                            </div>
                        </div>

                        <!-- Campaign Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Campaign Name
                            </label>
                            <input
                                type="text"
                                id="name"
                                v-model="form.name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                placeholder="e.g., February Newsletter"
                                required
                            />
                            <p class="mt-1 text-sm text-gray-500">Internal name to help you identify this campaign</p>
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <!-- Email Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700">
                                Email Subject
                            </label>
                            <input
                                type="text"
                                id="subject"
                                v-model="form.subject"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                placeholder="e.g., Check out our latest updates!"
                                required
                            />
                            <p class="mt-1 text-sm text-gray-500">The subject line recipients will see in their inbox</p>
                            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">{{ form.errors.subject }}</p>
                        </div>

                        <!-- Preview Text -->
                        <div>
                            <label for="preview_text" class="block text-sm font-medium text-gray-700">
                                Preview Text
                            </label>
                            <input
                                type="text"
                                id="preview_text"
                                v-model="form.preview_text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                                placeholder="e.g., This month's top stories and more..."
                                maxlength="500"
                            />
                            <p class="mt-1 text-sm text-gray-500">The preview text shown after the subject in most email clients</p>
                            <p v-if="form.errors.preview_text" class="mt-1 text-sm text-red-600">{{ form.errors.preview_text }}</p>
                        </div>

                        <!-- Email Content -->
                        <div>
                            <label for="content" class="block text-sm font-medium text-gray-700">
                                Email Content (HTML)
                            </label>
                            <textarea
                                id="content"
                                v-model="form.content"
                                rows="15"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm font-mono text-sm"
                                placeholder="Enter your email HTML content..."
                                required
                            ></textarea>
                            <p class="mt-1 text-sm text-gray-500">The HTML content of your email</p>
                            <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <Link
                                :href="route('admin.campaigns.index')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Campaign</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
