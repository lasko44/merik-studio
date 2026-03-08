<script setup lang="ts">
import { ref, computed, watch } from 'vue';

interface Props {
    title?: string;
    serviceType?: string;
    duration?: number;
    availableDays?: string[];
    startTime?: string;
    endTime?: string;
    slotInterval?: number;
    maxAdvanceDays?: number;
    product?: {
        id: number;
        name: string;
    };
}

const props = withDefaults(defineProps<Props>(), {
    title: 'Book an Appointment',
    serviceType: '',
    duration: 60,
    availableDays: () => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
    startTime: '09:00',
    endTime: '17:00',
    slotInterval: 30,
    maxAdvanceDays: 30,
});

const selectedDate = ref<Date | null>(null);
const selectedTime = ref<string | null>(null);
const customerName = ref('');
const customerEmail = ref('');
const customerPhone = ref('');
const notes = ref('');
const isSubmitting = ref(false);
const submitMessage = ref<{ type: 'success' | 'error'; text: string } | null>(null);
const currentMonth = ref(new Date());

const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const dayMapping: Record<string, number> = {
    sunday: 0,
    monday: 1,
    tuesday: 2,
    wednesday: 3,
    thursday: 4,
    friday: 5,
    saturday: 6,
};

const availableDayNumbers = computed(() => {
    return props.availableDays.map(day => dayMapping[day.toLowerCase()]);
});

const calendarDays = computed(() => {
    const year = currentMonth.value.getFullYear();
    const month = currentMonth.value.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startPadding = firstDay.getDay();
    const days: (Date | null)[] = [];

    // Add empty slots for padding
    for (let i = 0; i < startPadding; i++) {
        days.push(null);
    }

    // Add actual days
    for (let i = 1; i <= lastDay.getDate(); i++) {
        days.push(new Date(year, month, i));
    }

    return days;
});

const isDateAvailable = (date: Date | null): boolean => {
    if (!date) return false;

    const today = new Date();
    today.setHours(0, 0, 0, 0);

    const maxDate = new Date();
    maxDate.setDate(maxDate.getDate() + props.maxAdvanceDays);

    // Check if date is in the future and within max advance days
    if (date < today || date > maxDate) return false;

    // Check if day is in available days
    return availableDayNumbers.value.includes(date.getDay());
};

const timeSlots = computed(() => {
    const slots: string[] = [];
    const [startHour, startMinute] = props.startTime.split(':').map(Number);
    const [endHour, endMinute] = props.endTime.split(':').map(Number);

    const startMinutes = startHour * 60 + startMinute;
    const endMinutes = endHour * 60 + endMinute;

    for (let minutes = startMinutes; minutes < endMinutes; minutes += props.slotInterval) {
        const hour = Math.floor(minutes / 60);
        const min = minutes % 60;
        slots.push(`${hour.toString().padStart(2, '0')}:${min.toString().padStart(2, '0')}`);
    }

    return slots;
});

const formatTime = (time: string): string => {
    const [hour, minute] = time.split(':').map(Number);
    const period = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minute.toString().padStart(2, '0')} ${period}`;
};

const formatDate = (date: Date): string => {
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const monthYear = computed(() => {
    return currentMonth.value.toLocaleDateString('en-US', {
        month: 'long',
        year: 'numeric',
    });
});

const selectDate = (date: Date | null) => {
    if (date && isDateAvailable(date)) {
        selectedDate.value = date;
        selectedTime.value = null;
    }
};

const selectTime = (time: string) => {
    selectedTime.value = time;
};

const previousMonth = () => {
    const newDate = new Date(currentMonth.value);
    newDate.setMonth(newDate.getMonth() - 1);
    currentMonth.value = newDate;
};

const nextMonth = () => {
    const newDate = new Date(currentMonth.value);
    newDate.setMonth(newDate.getMonth() + 1);
    currentMonth.value = newDate;
};

const canGoPrevious = computed(() => {
    const today = new Date();
    return currentMonth.value.getMonth() > today.getMonth() ||
           currentMonth.value.getFullYear() > today.getFullYear();
});

const submitAppointment = async () => {
    if (!selectedDate.value || !selectedTime.value || !customerName.value || !customerEmail.value) {
        submitMessage.value = { type: 'error', text: 'Please fill in all required fields.' };
        return;
    }

    isSubmitting.value = true;
    submitMessage.value = null;

    // Simulate API call - in production, this would be a real endpoint
    await new Promise(resolve => setTimeout(resolve, 1500));

    submitMessage.value = {
        type: 'success',
        text: `Your appointment has been booked for ${formatDate(selectedDate.value)} at ${formatTime(selectedTime.value)}. We'll send a confirmation to ${customerEmail.value}.`
    };

    // Reset form
    selectedDate.value = null;
    selectedTime.value = null;
    customerName.value = '';
    customerEmail.value = '';
    customerPhone.value = '';
    notes.value = '';
    isSubmitting.value = false;
};

const isSelectedDate = (date: Date | null): boolean => {
    if (!date || !selectedDate.value) return false;
    return date.toDateString() === selectedDate.value.toDateString();
};

const isToday = (date: Date | null): boolean => {
    if (!date) return false;
    return date.toDateString() === new Date().toDateString();
};
</script>

<template>
    <section class="appointment-picker">
        <h2 v-if="title" class="text-2xl font-bold text-gray-900 mb-6">
            {{ title }}
        </h2>

        <p v-if="serviceType" class="text-gray-600 mb-4">
            Service: <span class="font-medium">{{ serviceType }}</span>
            <span v-if="duration" class="text-gray-500"> ({{ duration }} min)</span>
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Calendar -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <button
                        @click="previousMonth"
                        :disabled="!canGoPrevious"
                        class="p-2 rounded-lg hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h3 class="text-lg font-semibold text-gray-900">{{ monthYear }}</h3>
                    <button
                        @click="nextMonth"
                        class="p-2 rounded-lg hover:bg-gray-100 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-7 gap-1 mb-2">
                    <div
                        v-for="day in dayNames"
                        :key="day"
                        class="text-center text-sm font-medium text-gray-500 py-2"
                    >
                        {{ day }}
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-1">
                    <button
                        v-for="(date, index) in calendarDays"
                        :key="index"
                        :disabled="!isDateAvailable(date)"
                        @click="selectDate(date)"
                        :class="[
                            'aspect-square flex items-center justify-center text-sm rounded-lg transition-colors',
                            date === null ? 'invisible' : '',
                            isSelectedDate(date) ? 'bg-gray-900 text-white' : '',
                            isDateAvailable(date) && !isSelectedDate(date) ? 'hover:bg-gray-100 cursor-pointer' : '',
                            !isDateAvailable(date) && date !== null ? 'text-gray-300 cursor-not-allowed' : '',
                            isToday(date) && !isSelectedDate(date) ? 'ring-2 ring-gray-400' : '',
                        ]"
                    >
                        {{ date?.getDate() }}
                    </button>
                </div>
            </div>

            <!-- Time Slots & Form -->
            <div class="space-y-6">
                <!-- Time Slots -->
                <div v-if="selectedDate">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">
                        Available times for {{ formatDate(selectedDate) }}
                    </h4>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        <button
                            v-for="time in timeSlots"
                            :key="time"
                            @click="selectTime(time)"
                            :class="[
                                'py-2 px-3 text-sm font-medium rounded-lg border transition-colors',
                                selectedTime === time
                                    ? 'bg-gray-900 text-white border-gray-900'
                                    : 'border-gray-300 text-gray-700 hover:border-gray-400'
                            ]"
                        >
                            {{ formatTime(time) }}
                        </button>
                    </div>
                </div>

                <div v-else class="bg-gray-50 rounded-lg p-6 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Select a date to see available times</p>
                </div>

                <!-- Booking Form -->
                <div v-if="selectedDate && selectedTime" class="space-y-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600">
                            Selected: <span class="font-medium">{{ formatDate(selectedDate) }}</span> at
                            <span class="font-medium">{{ formatTime(selectedTime) }}</span>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="customer-name" class="block text-sm font-medium text-gray-700">Name *</label>
                            <input
                                id="customer-name"
                                v-model="customerName"
                                type="text"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                            />
                        </div>
                        <div>
                            <label for="customer-email" class="block text-sm font-medium text-gray-700">Email *</label>
                            <input
                                id="customer-email"
                                v-model="customerEmail"
                                type="email"
                                required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="customer-phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input
                            id="customer-phone"
                            v-model="customerPhone"
                            type="tel"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                        />
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea
                            id="notes"
                            v-model="notes"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-gray-700 focus:ring-gray-700 sm:text-sm"
                            placeholder="Any special requests or notes..."
                        ></textarea>
                    </div>

                    <button
                        @click="submitAppointment"
                        :disabled="isSubmitting"
                        class="w-full py-3 px-4 bg-gray-900 text-white rounded-lg font-semibold hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        {{ isSubmitting ? 'Booking...' : 'Book Appointment' }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Submit Message -->
        <div
            v-if="submitMessage"
            :class="[
                'mt-6 p-4 rounded-lg',
                submitMessage.type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'
            ]"
        >
            {{ submitMessage.text }}
        </div>
    </section>
</template>
