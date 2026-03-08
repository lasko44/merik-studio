<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

interface User {
    id: number;
    name: string;
    email: string;
}

interface Appointment {
    id: number;
    title: string;
    description: string | null;
    starts_at: string;
    ends_at: string;
    status: 'pending' | 'confirmed' | 'cancelled' | 'completed' | 'no_show';
    client_name: string;
    client_email: string | null;
    client_phone: string | null;
    user: User | null;
}

interface Statistics {
    today_count: number;
    upcoming_count: number;
    month_count: number;
    pending_count: number;
    confirmed_count: number;
}

interface Props {
    appointments: Appointment[];
    statistics: Statistics;
    staff: User[];
    currentStart: string;
    currentEnd: string;
}

const props = defineProps<Props>();

const currentDate = ref(new Date());
const viewMode = ref<'month' | 'week' | 'day'>('month');
const selectedAppointment = ref<Appointment | null>(null);

const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

const currentMonth = computed(() => currentDate.value.getMonth());
const currentYear = computed(() => currentDate.value.getFullYear());

const calendarDays = computed(() => {
    const year = currentYear.value;
    const month = currentMonth.value;

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startOffset = firstDay.getDay();

    const days: { date: Date; isCurrentMonth: boolean; appointments: Appointment[] }[] = [];

    // Previous month days
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    for (let i = startOffset - 1; i >= 0; i--) {
        const date = new Date(year, month - 1, prevMonthLastDay - i);
        days.push({
            date,
            isCurrentMonth: false,
            appointments: getAppointmentsForDate(date),
        });
    }

    // Current month days
    for (let day = 1; day <= lastDay.getDate(); day++) {
        const date = new Date(year, month, day);
        days.push({
            date,
            isCurrentMonth: true,
            appointments: getAppointmentsForDate(date),
        });
    }

    // Next month days
    const remainingDays = 42 - days.length;
    for (let day = 1; day <= remainingDays; day++) {
        const date = new Date(year, month + 1, day);
        days.push({
            date,
            isCurrentMonth: false,
            appointments: getAppointmentsForDate(date),
        });
    }

    return days;
});

const getAppointmentsForDate = (date: Date): Appointment[] => {
    const dateStr = date.toISOString().split('T')[0];
    return props.appointments.filter((apt) => {
        const aptDate = new Date(apt.starts_at).toISOString().split('T')[0];
        return aptDate === dateStr;
    });
};

const isToday = (date: Date): boolean => {
    const today = new Date();
    return date.toDateString() === today.toDateString();
};

const previousMonth = () => {
    currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1);
    loadAppointments();
};

const nextMonth = () => {
    currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1);
    loadAppointments();
};

const goToToday = () => {
    currentDate.value = new Date();
    loadAppointments();
};

const loadAppointments = () => {
    const start = new Date(currentYear.value, currentMonth.value, 1);
    const end = new Date(currentYear.value, currentMonth.value + 1, 0);

    router.get(route('admin.calendar.index'), {
        start: start.toISOString().split('T')[0],
        end: end.toISOString().split('T')[0],
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const statusColors: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    confirmed: 'bg-green-100 text-green-800 border-green-200',
    cancelled: 'bg-red-100 text-red-800 border-red-200',
    completed: 'bg-blue-100 text-blue-800 border-blue-200',
    no_show: 'bg-gray-100 text-gray-800 border-gray-200',
};

const formatTime = (dateStr: string): string => {
    return new Date(dateStr).toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
    });
};

const deleteAppointment = (id: number) => {
    if (confirm('Are you sure you want to delete this appointment?')) {
        router.delete(route('admin.calendar.destroy', id));
    }
};
</script>

<template>
    <Head title="Calendar" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Calendar</h2>
                <Link
                    :href="route('admin.calendar.create')"
                    class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700"
                >
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                    </svg>
                    New Appointment
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Statistics -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Today</p>
                        <p class="text-2xl font-bold text-gray-900">{{ statistics.today_count }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Upcoming</p>
                        <p class="text-2xl font-bold text-gray-900">{{ statistics.upcoming_count }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">This Month</p>
                        <p class="text-2xl font-bold text-gray-900">{{ statistics.month_count }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Pending</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ statistics.pending_count }}</p>
                    </div>
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                        <p class="text-sm text-gray-500">Confirmed</p>
                        <p class="text-2xl font-bold text-green-600">{{ statistics.confirmed_count }}</p>
                    </div>
                </div>

                <!-- Calendar -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <!-- Calendar Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center gap-4">
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ monthNames[currentMonth] }} {{ currentYear }}
                            </h2>
                            <button
                                @click="goToToday"
                                class="px-3 py-1 text-sm font-medium text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200"
                            >
                                Today
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="previousMonth"
                                class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button
                                @click="nextMonth"
                                class="p-2 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Calendar Grid -->
                    <div class="grid grid-cols-7">
                        <!-- Day Headers -->
                        <div
                            v-for="day in dayNames"
                            :key="day"
                            class="px-2 py-3 text-center text-sm font-medium text-gray-500 border-b border-gray-200 bg-gray-50"
                        >
                            {{ day }}
                        </div>

                        <!-- Calendar Days -->
                        <div
                            v-for="(day, index) in calendarDays"
                            :key="index"
                            :class="[
                                'min-h-[120px] p-2 border-b border-r border-gray-200',
                                day.isCurrentMonth ? 'bg-white' : 'bg-gray-50',
                                isToday(day.date) ? 'bg-indigo-50' : '',
                            ]"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span
                                    :class="[
                                        'text-sm font-medium',
                                        day.isCurrentMonth ? 'text-gray-900' : 'text-gray-400',
                                        isToday(day.date) ? 'bg-gray-800 text-white rounded-full w-7 h-7 flex items-center justify-center' : '',
                                    ]"
                                >
                                    {{ day.date.getDate() }}
                                </span>
                            </div>

                            <!-- Appointments for this day -->
                            <div class="space-y-1">
                                <Link
                                    v-for="apt in day.appointments.slice(0, 3)"
                                    :key="apt.id"
                                    :href="route('admin.calendar.edit', apt.id)"
                                    :class="[
                                        'block px-2 py-1 text-xs rounded border truncate',
                                        statusColors[apt.status],
                                    ]"
                                >
                                    {{ formatTime(apt.starts_at) }} {{ apt.title }}
                                </Link>
                                <div
                                    v-if="day.appointments.length > 3"
                                    class="text-xs text-gray-500 pl-2"
                                >
                                    +{{ day.appointments.length - 3 }} more
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments List -->
                <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Upcoming Appointments</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        <div
                            v-for="apt in appointments.filter(a => new Date(a.starts_at) >= new Date()).slice(0, 10)"
                            :key="apt.id"
                            class="px-6 py-4 flex items-center justify-between hover:bg-gray-50"
                        >
                            <div class="flex items-center gap-4">
                                <div :class="['w-3 h-3 rounded-full', statusColors[apt.status].split(' ')[0]]"></div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ apt.title }}</p>
                                    <p class="text-sm text-gray-500">
                                        {{ new Date(apt.starts_at).toLocaleDateString() }} at {{ formatTime(apt.starts_at) }}
                                        - {{ apt.client_name }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <Link
                                    :href="route('admin.calendar.edit', apt.id)"
                                    class="text-gray-700 hover:text-indigo-800 text-sm font-medium"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deleteAppointment(apt.id)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                        <div v-if="appointments.length === 0" class="px-6 py-8 text-center text-gray-500">
                            No appointments scheduled
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
