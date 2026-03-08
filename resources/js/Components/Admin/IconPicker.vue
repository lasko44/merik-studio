<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch, nextTick } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { icons, getIconPath } from '@/utils/icons';

export interface IconValue {
    icon: string;
    showBackground: boolean;
    color: 'primary' | 'secondary' | 'accent';
}

interface Props {
    modelValue: string | IconValue;
    label?: string;
}

interface ThemeColors {
    primaryColor?: string;
    secondaryColor?: string;
    accentColor?: string;
    primary_color?: string;
    secondary_color?: string;
    accent_color?: string;
}

const props = withDefaults(defineProps<Props>(), {
    modelValue: '',
    label: 'Icon',
});

const emit = defineEmits<{
    'update:modelValue': [value: IconValue];
}>();

const isOpen = ref(false);
const searchQuery = ref('');
const dropdownRef = ref<HTMLElement | null>(null);
const buttonRef = ref<HTMLElement | null>(null);
const dropdownStyle = ref<Record<string, string>>({});

// Parse model value - handle both string and object formats
const parsedValue = computed((): IconValue => {
    if (typeof props.modelValue === 'string') {
        return {
            icon: props.modelValue,
            showBackground: true,
            color: 'primary',
        };
    }
    return {
        icon: props.modelValue.icon || '',
        showBackground: props.modelValue.showBackground ?? true,
        color: props.modelValue.color || 'primary',
    };
});

// Get theme colors from page props
const themeColors = computed(() => {
    const page = usePage();
    const settings = (page.props as Record<string, unknown>).siteSettings as ThemeColors | undefined;
    return {
        primary: settings?.primaryColor || settings?.primary_color || '#6366F1',
        secondary: settings?.secondaryColor || settings?.secondary_color || '#10B981',
        accent: settings?.accentColor || settings?.accent_color || '#F59E0B',
    };
});

const currentColor = computed(() => themeColors.value[parsedValue.value.color]);

// Filtered icons based on search
const filteredIcons = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) {
        return Object.entries(icons);
    }
    return Object.entries(icons).filter(([name, { keywords }]) => {
        return name.includes(query) || keywords.some(kw => kw.includes(query));
    });
});

const selectedIcon = computed(() => parsedValue.value.icon);

const updateValue = (updates: Partial<IconValue>) => {
    emit('update:modelValue', {
        ...parsedValue.value,
        ...updates,
    });
};

const selectIcon = (iconName: string) => {
    updateValue({ icon: iconName });
    isOpen.value = false;
    searchQuery.value = '';
};

const toggleBackground = () => {
    updateValue({ showBackground: !parsedValue.value.showBackground });
};

const setColor = (color: 'primary' | 'secondary' | 'accent') => {
    updateValue({ color });
};

// Calculate dropdown position using fixed positioning
const updateDropdownPosition = () => {
    if (!buttonRef.value) return;

    const rect = buttonRef.value.getBoundingClientRect();
    const viewportHeight = window.innerHeight;
    const dropdownHeight = 350; // Approximate max height

    // Check if dropdown would go off bottom of screen
    const spaceBelow = viewportHeight - rect.bottom;
    const openUpward = spaceBelow < dropdownHeight && rect.top > dropdownHeight;

    dropdownStyle.value = {
        position: 'fixed',
        left: `${rect.left}px`,
        width: `${rect.width}px`,
        zIndex: '9999',
        ...(openUpward
            ? { bottom: `${viewportHeight - rect.top + 4}px` }
            : { top: `${rect.bottom + 4}px` }
        ),
    };
};

// Close dropdown when clicking outside
const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as Node;
    if (
        dropdownRef.value &&
        !dropdownRef.value.contains(target) &&
        buttonRef.value &&
        !buttonRef.value.contains(target)
    ) {
        isOpen.value = false;
    }
};

// Update position on scroll/resize
const handleScrollOrResize = () => {
    if (isOpen.value) {
        updateDropdownPosition();
    }
};

watch(isOpen, async (newValue) => {
    if (newValue) {
        await nextTick();
        updateDropdownPosition();
    }
});

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
    window.addEventListener('scroll', handleScrollOrResize, true);
    window.addEventListener('resize', handleScrollOrResize);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
    window.removeEventListener('scroll', handleScrollOrResize, true);
    window.removeEventListener('resize', handleScrollOrResize);
});
</script>

<template>
    <div class="space-y-2">
        <!-- Icon Selection Button -->
        <div ref="buttonRef">
            <button
                type="button"
                @click="isOpen = !isOpen"
                class="flex items-center gap-3 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-700 focus:border-gray-700"
                :aria-expanded="isOpen"
                aria-haspopup="listbox"
            >
                <!-- Icon Preview -->
                <div
                    class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0"
                    :style="parsedValue.showBackground ? { backgroundColor: currentColor } : {}"
                    :class="{ 'border border-gray-200': !parsedValue.showBackground }"
                    aria-hidden="true"
                >
                    <svg
                        v-if="selectedIcon && icons[selectedIcon]"
                        class="w-5 h-5"
                        :class="parsedValue.showBackground ? 'text-white' : ''"
                        :style="!parsedValue.showBackground ? { color: currentColor } : {}"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            :d="getIconPath(selectedIcon)"
                        />
                    </svg>
                    <span v-else class="text-gray-400 text-xs">?</span>
                </div>

                <!-- Icon Name -->
                <span class="flex-1 text-left text-sm text-gray-700">
                    {{ selectedIcon || 'Select an icon...' }}
                </span>

                <!-- Dropdown Arrow -->
                <svg
                    class="w-5 h-5 text-gray-400 transition-transform"
                    :class="{ 'rotate-180': isOpen }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <!-- Style Options -->
        <div class="flex items-center gap-4 pl-1">
            <!-- Background Toggle -->
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    type="checkbox"
                    :checked="parsedValue.showBackground"
                    @change="toggleBackground"
                    class="rounded border-gray-300 text-gray-700 shadow-sm focus:ring-gray-700"
                />
                <span class="text-sm text-gray-600">Background</span>
            </label>

            <!-- Color Selection -->
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Color:</span>
                <div class="flex gap-1" role="radiogroup" aria-label="Icon color">
                    <button
                        type="button"
                        @click="setColor('primary')"
                        class="w-6 h-6 rounded-full border-2 transition-all"
                        :class="parsedValue.color === 'primary' ? 'border-gray-800 scale-110' : 'border-transparent hover:border-gray-300'"
                        :style="{ backgroundColor: themeColors.primary }"
                        title="Primary"
                        aria-label="Primary color"
                        role="radio"
                        :aria-checked="parsedValue.color === 'primary'"
                    />
                    <button
                        type="button"
                        @click="setColor('secondary')"
                        class="w-6 h-6 rounded-full border-2 transition-all"
                        :class="parsedValue.color === 'secondary' ? 'border-gray-800 scale-110' : 'border-transparent hover:border-gray-300'"
                        :style="{ backgroundColor: themeColors.secondary }"
                        title="Secondary"
                        aria-label="Secondary color"
                        role="radio"
                        :aria-checked="parsedValue.color === 'secondary'"
                    />
                    <button
                        type="button"
                        @click="setColor('accent')"
                        class="w-6 h-6 rounded-full border-2 transition-all"
                        :class="parsedValue.color === 'accent' ? 'border-gray-800 scale-110' : 'border-transparent hover:border-gray-300'"
                        :style="{ backgroundColor: themeColors.accent }"
                        title="Accent"
                        aria-label="Accent color"
                        role="radio"
                        :aria-checked="parsedValue.color === 'accent'"
                    />
                </div>
            </div>
        </div>

        <!-- Dropdown (using Teleport for fixed positioning) -->
        <Teleport to="body">
            <div
                v-if="isOpen"
                ref="dropdownRef"
                :style="dropdownStyle"
                class="bg-white border border-gray-200 rounded-md shadow-lg"
            >
                <!-- Search Input -->
                <div class="p-2 border-b border-gray-200">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Search icons..."
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-700 focus:border-gray-700"
                        @click.stop
                    />
                </div>

                <!-- Icons Grid -->
                <div class="max-h-64 overflow-y-auto p-2" role="listbox" aria-label="Available icons">
                    <div v-if="filteredIcons.length === 0" class="py-4 text-center text-sm text-gray-500">
                        No icons found
                    </div>
                    <div v-else class="grid grid-cols-5 gap-1">
                        <button
                            v-for="[name, { path }] in filteredIcons"
                            :key="name"
                            type="button"
                            @click="selectIcon(name)"
                            :class="[
                                'flex flex-col items-center gap-1 p-2 rounded-md transition-colors',
                                selectedIcon === name
                                    ? 'bg-gray-100 ring-2 ring-gray-700'
                                    : 'hover:bg-gray-100'
                            ]"
                            :title="name"
                            role="option"
                            :aria-selected="selectedIcon === name"
                            :aria-label="name"
                        >
                            <div
                                class="w-8 h-8 rounded-md flex items-center justify-center"
                                :style="parsedValue.showBackground ? { backgroundColor: currentColor } : {}"
                                :class="{ 'border border-gray-200': !parsedValue.showBackground }"
                                aria-hidden="true"
                            >
                                <svg
                                    class="w-5 h-5"
                                    :class="parsedValue.showBackground ? 'text-white' : ''"
                                    :style="!parsedValue.showBackground ? { color: currentColor } : {}"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        :d="path"
                                    />
                                </svg>
                            </div>
                            <span class="text-xs text-gray-600 truncate w-full text-center" aria-hidden="true">{{ name }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>
