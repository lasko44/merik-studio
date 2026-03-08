<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Role {
    id: number;
    name: string;
}

interface User {
    id: number;
    name: string;
    email: string;
    email_verified_at: string | null;
    created_at: string;
    roles: Role[];
}

interface Invitation {
    id: number;
    email: string;
    role: string;
    status: string;
    expires_at: string;
    created_at: string;
    inviter: {
        id: number;
        name: string;
    };
}

interface PaginatedUsers {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface PaginatedInvitations {
    data: Invitation[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: { url: string | null; label: string; active: boolean }[];
}

interface Statistics {
    total: number;
    admins: number;
    users: number;
    customers: number;
}

interface InvitationStatistics {
    pending: number;
    accepted: number;
    expired: number;
    revoked: number;
}

interface Props {
    users: PaginatedUsers;
    statistics: Statistics;
    invitationStatistics: InvitationStatistics;
    pendingInvitations: PaginatedInvitations;
    roles: Role[];
}

const props = defineProps<Props>();

const showInviteModal = ref(false);

const inviteForm = useForm({
    email: '',
    role: '',
});

const roleColors: Record<string, string> = {
    'Super Admin': 'bg-purple-100 text-purple-800',
    'Admin': 'bg-indigo-100 text-indigo-800',
    'User': 'bg-blue-100 text-blue-800',
    'Customer': 'bg-gray-100 text-gray-800',
};

const formatDate = (dateStr: string): string => {
    return new Date(dateStr).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
};

const deleteUser = (id: number) => {
    if (confirm('Are you sure you want to delete this user?')) {
        router.delete(route('admin.users.destroy', id));
    }
};

const openInviteModal = () => {
    inviteForm.reset();
    showInviteModal.value = true;
};

const closeInviteModal = () => {
    showInviteModal.value = false;
    inviteForm.reset();
};

const submitInvite = () => {
    inviteForm.post(route('admin.users.invite'), {
        onSuccess: () => {
            closeInviteModal();
        },
    });
};

const resendInvitation = (id: number) => {
    router.post(route('admin.users.invitations.resend', id));
};

const revokeInvitation = (id: number) => {
    if (confirm('Are you sure you want to revoke this invitation?')) {
        router.delete(route('admin.users.invitations.destroy', id));
    }
};

const isExpired = (expiresAt: string): boolean => {
    return new Date(expiresAt) < new Date();
};
</script>

<template>
    <Head title="Users" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Users</h2>
                <div class="flex items-center gap-2">
                    <button
                        @click="openInviteModal"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M3 4a2 2 0 00-2 2v1.161l8.441 4.221a1.25 1.25 0 001.118 0L19 7.162V6a2 2 0 00-2-2H3z" />
                            <path d="M19 8.839l-7.77 3.885a2.75 2.75 0 01-2.46 0L1 8.839V14a2 2 0 002 2h14a2 2 0 002-2V8.839z" />
                        </svg>
                        Invite User
                    </button>
                    <Link
                        :href="route('admin.users.create')"
                        class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                    >
                        <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                        </svg>
                        Add User
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ statistics.total }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Admins</p>
                        <p class="text-2xl font-bold text-gray-700">{{ statistics.admins }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Users</p>
                        <p class="text-2xl font-bold text-blue-600">{{ statistics.users }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Customers</p>
                        <p class="text-2xl font-bold text-gray-600">{{ statistics.customers }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Pending Invitations</p>
                        <p class="text-2xl font-bold text-amber-600">{{ invitationStatistics.pending }}</p>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Role
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Joined
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                            <span class="text-gray-700 font-medium text-sm">
                                                {{ user.name.charAt(0).toUpperCase() }}
                                            </span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                            <div class="text-sm text-gray-500">{{ user.email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        v-for="role in user.roles"
                                        :key="role.id"
                                        :class="['px-2 py-1 text-xs font-medium rounded-full', roleColors[role.name] || 'bg-gray-100 text-gray-800']"
                                    >
                                        {{ role.name }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        :class="[
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            user.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                        ]"
                                    >
                                        {{ user.email_verified_at ? 'Verified' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(user.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('admin.users.edit', user.id)"
                                            class="text-gray-700 hover:text-gray-900"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="deleteUser(user.id)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No users found
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div v-if="users.last_page > 1" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-gray-700">
                                Showing {{ (users.current_page - 1) * users.per_page + 1 }} to
                                {{ Math.min(users.current_page * users.per_page, users.total) }} of
                                {{ users.total }} users
                            </p>
                            <nav class="flex items-center gap-1">
                                <template v-for="link in users.links" :key="link.label">
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

                <!-- Pending Invitations -->
                <div v-if="pendingInvitations.data.length > 0" class="mt-8">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Invitations</h3>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Invited By
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sent
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <span class="sr-only">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="invitation in pendingInvitations.data" :key="invitation.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ invitation.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="['px-2 py-1 text-xs font-medium rounded-full', roleColors[invitation.role] || 'bg-gray-100 text-gray-800']"
                                        >
                                            {{ invitation.role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ invitation.inviter.name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="[
                                                'px-2 py-1 text-xs font-medium rounded-full',
                                                isExpired(invitation.expires_at)
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-amber-100 text-amber-800'
                                            ]"
                                        >
                                            {{ isExpired(invitation.expires_at) ? 'Expired' : 'Pending' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ formatDate(invitation.created_at) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                @click="resendInvitation(invitation.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                            >
                                                Resend
                                            </button>
                                            <button
                                                @click="revokeInvitation(invitation.id)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Revoke
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invite User Modal -->
        <div v-if="showInviteModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" @click="closeInviteModal">
                    <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
                    @click.stop
                >
                    <div>
                        <div class="text-center">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Invite User</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Send an invitation email to a new user.
                            </p>
                        </div>
                        <form @submit.prevent="submitInvite" class="mt-6 space-y-4">
                            <div>
                                <InputLabel for="invite-email" value="Email Address" />
                                <TextInput
                                    id="invite-email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="inviteForm.email"
                                    required
                                    autofocus
                                />
                                <InputError class="mt-2" :message="inviteForm.errors.email" />
                            </div>
                            <div>
                                <InputLabel for="invite-role" value="Role" />
                                <select
                                    id="invite-role"
                                    v-model="inviteForm.role"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    required
                                >
                                    <option value="">Select a role</option>
                                    <option v-for="role in roles" :key="role.id" :value="role.name">
                                        {{ role.name }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="inviteForm.errors.role" />
                            </div>
                            <div class="mt-6 flex justify-end gap-3">
                                <button
                                    type="button"
                                    @click="closeInviteModal"
                                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50"
                                >
                                    Cancel
                                </button>
                                <PrimaryButton
                                    :class="{ 'opacity-25': inviteForm.processing }"
                                    :disabled="inviteForm.processing"
                                >
                                    Send Invitation
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
