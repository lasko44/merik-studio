<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Status {
    value: string;
    label: string;
}

interface Appointment {
    id: number;
    title: string;
    description: string | null;
    starts_at: string;
    ends_at: string;
    status: string;
    client_name: string;
    client_email: string | null;
    client_phone: string | null;
    notes: string | null;
    user_id: number | null;
    service_type: string | null;
    user: User | null;
}

interface Props {
    appointment: Appointment;
    staff: User[];
    statuses: Status[];
}

const props = defineProps<Props>();

const formatDateTimeLocal = (dateStr: string): string => {
    const date = new Date(dateStr);
    return date.toISOString().slice(0, 16);
};

const form = useForm({
    title: props.appointment.title,
    description: props.appointment.description || '',
    starts_at: formatDateTimeLocal(props.appointment.starts_at),
    ends_at: formatDateTimeLocal(props.appointment.ends_at),
    status: props.appointment.status,
    client_name: props.appointment.client_name,
    client_email: props.appointment.client_email || '',
    client_phone: props.appointment.client_phone || '',
    notes: props.appointment.notes || '',
    user_id: props.appointment.user_id?.toString() || '',
    service_type: props.appointment.service_type || '',
});

const submit = () => {
    form.put(route('admin.calendar.update', props.appointment.id));
};

const deleteAppointment = () => {
    if (confirm('Are you sure you want to delete this appointment?')) {
        router.delete(route('admin.calendar.destroy', props.appointment.id));
    }
};

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    completed: 'bg-blue-100 text-blue-800',
    no_show: 'bg-gray-100 text-gray-800',
};
</script>

<template>
    <Head title="Edit Appointment" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-sm">
                    <Link :href="route('admin.calendar.index')" class="text-gray-500 hover:text-gray-700">
                        Calendar
                    </Link>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                    <span class="text-gray-900 font-medium">{{ appointment.title }}</span>
                    <span :class="['ml-2 px-2 py-0.5 text-xs font-medium rounded-full', statusColors[appointment.status]]">
                        {{ appointment.status }}
                    </span>
                </div>
                <button
                    @click="deleteAppointment"
                    class="inline-flex items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500"
                >
                    Delete
                </button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Title -->
                        <div>
                            <InputLabel for="title" value="Title" />
                            <TextInput
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError :message="form.errors.title" class="mt-2" />
                        </div>

                        <!-- Date/Time -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="starts_at" value="Start Date & Time" />
                                <TextInput
                                    id="starts_at"
                                    v-model="form.starts_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.starts_at" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="ends_at" value="End Date & Time" />
                                <TextInput
                                    id="ends_at"
                                    v-model="form.ends_at"
                                    type="datetime-local"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError :message="form.errors.ends_at" class="mt-2" />
                            </div>
                        </div>

                        <!-- Client Info -->
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Client Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel for="client_name" value="Client Name" />
                                    <TextInput
                                        id="client_name"
                                        v-model="form.client_name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        required
                                    />
                                    <InputError :message="form.errors.client_name" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="client_email" value="Client Email" />
                                    <TextInput
                                        id="client_email"
                                        v-model="form.client_email"
                                        type="email"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.client_email" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="client_phone" value="Client Phone" />
                                    <TextInput
                                        id="client_phone"
                                        v-model="form.client_phone"
                                        type="tel"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.client_phone" class="mt-2" />
                                </div>
                                <div>
                                    <InputLabel for="service_type" value="Service Type" />
                                    <TextInput
                                        id="service_type"
                                        v-model="form.service_type"
                                        type="text"
                                        class="mt-1 block w-full"
                                    />
                                    <InputError :message="form.errors.service_type" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <!-- Assignment -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel for="user_id" value="Assign To" />
                                <select
                                    id="user_id"
                                    v-model="form.user_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
                                >
                                    <option value="">Unassigned</option>
                                    <option v-for="user in staff" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.user_id" class="mt-2" />
                            </div>
                            <div>
                                <InputLabel for="status" value="Status" />
                                <select
                                    id="status"
                                    v-model="form.status"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
                                >
                                    <option v-for="status in statuses" :key="status.value" :value="status.value">
                                        {{ status.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.status" class="mt-2" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <InputLabel for="description" value="Description" />
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
                            ></textarea>
                            <InputError :message="form.errors.description" class="mt-2" />
                        </div>

                        <!-- Notes -->
                        <div>
                            <InputLabel for="notes" value="Internal Notes" />
                            <textarea
                                id="notes"
                                v-model="form.notes"
                                rows="2"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700"
                            ></textarea>
                            <InputError :message="form.errors.notes" class="mt-2" />
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                            <Link
                                :href="route('admin.calendar.index')"
                                class="text-sm text-gray-600 hover:text-gray-900"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 disabled:opacity-50"
                            >
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
