<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

interface User {
    id: number;
    name: string;
}

interface Campaign {
    id: number;
    name: string;
    subject: string;
    status: 'draft' | 'scheduled' | 'sending' | 'sent' | 'cancelled';
    scheduled_at: string | null;
    sent_at: string | null;
    total_recipients: number;
    sent_count: number;
    opened_count: number;
    clicked_count: number;
    open_rate: number;
    click_rate: number;
    creator: User | null;
    created_at: string;
}

interface PaginatedCampaigns {
    data: Campaign[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Statistics {
    total: number;
    draft: number;
    sent: number;
    subscribers: number;
}

interface Props {
    campaigns: PaginatedCampaigns;
    statistics: Statistics;
}

const props = defineProps<Props>();

const statusColors: Record<string, string> = {
    draft: 'bg-gray-100 text-gray-800',
    scheduled: 'bg-blue-100 text-blue-800',
    sending: 'bg-yellow-100 text-yellow-800',
    sent: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
};

const formatDate = (dateStr: string | null): string => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
};

const deleteCampaign = (id: number) => {
    if (confirm('Are you sure you want to delete this campaign?')) {
        router.delete(route('admin.campaigns.destroy', id));
    }
};
</script>

<template>
    <Head title="Email Campaigns" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Email Campaigns</h2>
                <Link
                    :href="route('admin.campaigns.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                >
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    New Campaign
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Total Campaigns</p>
                        <p class="text-2xl font-bold text-gray-900">{{ statistics.total }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Drafts</p>
                        <p class="text-2xl font-bold text-gray-600">{{ statistics.draft }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Sent</p>
                        <p class="text-2xl font-bold text-green-600">{{ statistics.sent }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Subscribers</p>
                        <p class="text-2xl font-bold text-gray-700">{{ statistics.subscribers }}</p>
                    </div>
                </div>

                <!-- Campaigns Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Campaign
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Performance
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="campaign in campaigns.data" :key="campaign.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ campaign.name }}</div>
                                        <div class="text-sm text-gray-500">{{ campaign.subject }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="['px-2 py-1 text-xs font-medium rounded-full capitalize', statusColors[campaign.status]]"
                                    >
                                        {{ campaign.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <template v-if="campaign.status === 'sent'">
                                        <div class="text-sm">
                                            <span class="text-gray-900">{{ campaign.sent_count }}</span>
                                            <span class="text-gray-500"> sent</span>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ campaign.open_rate }}% opened, {{ campaign.click_rate }}% clicked
                                        </div>
                                    </template>
                                    <span v-else class="text-sm text-gray-500">-</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <template v-if="campaign.sent_at">
                                        {{ formatDate(campaign.sent_at) }}
                                    </template>
                                    <template v-else-if="campaign.scheduled_at">
                                        <span class="text-blue-600">{{ formatDate(campaign.scheduled_at) }}</span>
                                    </template>
                                    <template v-else>
                                        {{ formatDate(campaign.created_at) }}
                                    </template>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            v-if="campaign.status === 'draft' || campaign.status === 'scheduled'"
                                            :href="route('admin.campaigns.edit', campaign.id)"
                                            class="text-gray-700 hover:text-gray-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="deleteCampaign(campaign.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="campaigns.data.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No campaigns yet. Create your first email campaign!
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="campaigns.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-700">
                                Showing {{ (campaigns.current_page - 1) * campaigns.per_page + 1 }} to
                                {{ Math.min(campaigns.current_page * campaigns.per_page, campaigns.total) }} of
                                {{ campaigns.total }} campaigns
                            </p>
                            <nav class="flex items-center gap-1">
                                <template v-for="link in campaigns.links" :key="link.label">
                                    <Link
                                        v-if="link.url"
                                        :href="link.url"
                                        :class="[
                                            'px-3 py-1 text-sm rounded',
                                            link.active
                                                ? 'bg-gray-800 text-white'
                                                : 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                                        ]"
                                        v-html="link.label"
                                    />
                                    <span
                                        v-else
                                        class="px-3 py-1 text-sm text-gray-400"
                                        v-html="link.label"
                                    />
                                </template>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
