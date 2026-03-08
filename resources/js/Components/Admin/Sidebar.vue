<script setup lang="ts">
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import ApplicationIcon from '@/Components/ApplicationIcon.vue';

interface NavItem {
    name: string;
    href: string;
    icon: string;
    active: boolean;
}

interface NavGroup {
    label: string;
    items: NavItem[];
}

const emit = defineEmits<{
    'update:collapsed': [value: boolean];
}>();

const collapsed = ref(false);

const toggleCollapse = () => {
    collapsed.value = !collapsed.value;
    emit('update:collapsed', collapsed.value);
};

const navGroups = computed<NavGroup[]>(() => [
    {
        label: 'Main',
        items: [
            {
                name: 'Dashboard',
                href: route('admin.dashboard'),
                icon: 'dashboard',
                active: route().current('admin.dashboard'),
            },
        ],
    },
    {
        label: 'Content',
        items: [
            {
                name: 'Pages',
                href: route('admin.pages.index'),
                icon: 'pages',
                active: route().current('admin.pages.*'),
            },
            {
                name: 'Blog',
                href: route('admin.blog.posts.index'),
                icon: 'blog',
                active: route().current('admin.blog.*'),
            },
            {
                name: 'Products',
                href: route('admin.products.index'),
                icon: 'products',
                active: route().current('admin.products.*'),
            },
        ],
    },
    {
        label: 'Marketing',
        items: [
            {
                name: 'Calendar',
                href: route('admin.calendar.index'),
                icon: 'calendar',
                active: route().current('admin.calendar.*'),
            },
            {
                name: 'Campaigns',
                href: route('admin.campaigns.index'),
                icon: 'campaigns',
                active: route().current('admin.campaigns.*'),
            },
        ],
    },
    {
        label: 'Admin',
        items: [
            {
                name: 'Setup Wizard',
                href: route('admin.setup'),
                icon: 'wizard',
                active: route().current('admin.setup*'),
            },
            {
                name: 'Navigation',
                href: route('admin.navigation.edit'),
                icon: 'navigation',
                active: route().current('admin.navigation.*'),
            },
            {
                name: 'Users',
                href: route('admin.users.index'),
                icon: 'users',
                active: route().current('admin.users.*'),
            },
            {
                name: 'Settings',
                href: route('admin.settings.edit'),
                icon: 'settings',
                active: route().current('admin.settings.*'),
            },
            {
                name: 'Updates',
                href: route('admin.updates.index'),
                icon: 'updates',
                active: route().current('admin.updates.*'),
            },
        ],
    },
]);

const bottomNavItems = computed<NavItem[]>(() => [
    {
        name: 'Help',
        href: route('admin.help.index'),
        icon: 'help',
        active: route().current('admin.help.*'),
    },
]);
</script>

<template>
    <aside
        :class="[
            'fixed left-0 top-0 z-40 h-screen bg-gray-900 transition-all duration-300 flex flex-col overflow-visible',
            collapsed ? 'w-16' : 'w-64'
        ]"
    >
        <!-- Logo & Collapse Toggle -->
        <div class="relative flex h-16 items-center px-4 border-b border-gray-800">
            <Link :href="route('admin.dashboard')" class="flex items-center gap-3">
                <ApplicationIcon class="h-8 w-8 text-white flex-shrink-0" />
                <span
                    v-if="!collapsed"
                    class="font-semibold text-white whitespace-nowrap transition-opacity duration-200"
                >
                    Merik Studio
                </span>
            </Link>
            <!-- Collapse toggle positioned at sidebar edge -->
            <button
                @click="toggleCollapse"
                class="absolute -right-3 top-1/2 -translate-y-1/2 flex h-6 w-6 items-center justify-center rounded-full bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white border border-gray-600 shadow-md transition-colors"
                :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'"
            >
                <svg
                    class="h-3.5 w-3.5 transition-transform duration-300"
                    :class="{ 'rotate-180': collapsed }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto py-4 px-3">
            <div v-for="group in navGroups" :key="group.label" class="mb-6">
                <p
                    v-if="!collapsed"
                    class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider"
                >
                    {{ group.label }}
                </p>
                <div v-else class="mb-2 border-t border-gray-800"></div>

                <ul class="space-y-1">
                    <li v-for="item in group.items" :key="item.name">
                        <Link
                            :href="item.href"
                            :class="[
                                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                item.active
                                    ? 'bg-gray-800 text-white'
                                    : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                                collapsed ? 'justify-center' : ''
                            ]"
                            :title="collapsed ? item.name : undefined"
                        >
                            <!-- Dashboard Icon -->
                            <svg v-if="item.icon === 'dashboard'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <!-- Pages Icon -->
                            <svg v-else-if="item.icon === 'pages'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <!-- Blog Icon -->
                            <svg v-else-if="item.icon === 'blog'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                            <!-- Products Icon -->
                            <svg v-else-if="item.icon === 'products'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            <!-- Calendar Icon -->
                            <svg v-else-if="item.icon === 'calendar'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <!-- Campaigns Icon -->
                            <svg v-else-if="item.icon === 'campaigns'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <!-- Wizard Icon -->
                            <svg v-else-if="item.icon === 'wizard'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                            </svg>
                            <!-- Navigation Icon -->
                            <svg v-else-if="item.icon === 'navigation'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            <!-- Users Icon -->
                            <svg v-else-if="item.icon === 'users'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <!-- Settings Icon -->
                            <svg v-else-if="item.icon === 'settings'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <!-- Help Icon -->
                            <svg v-else-if="item.icon === 'help'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <!-- Updates Icon -->
                            <svg v-else-if="item.icon === 'updates'" class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>

                            <span v-if="!collapsed" class="truncate">{{ item.name }}</span>
                        </Link>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Bottom Section -->
        <div class="border-t border-gray-800 p-3">
            <!-- Help Link -->
            <Link
                v-for="item in bottomNavItems"
                :key="item.name"
                :href="item.href"
                :class="[
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors mb-2',
                    item.active
                        ? 'bg-gray-800 text-white'
                        : 'text-gray-400 hover:bg-gray-800 hover:text-white',
                    collapsed ? 'justify-center' : ''
                ]"
                :title="collapsed ? item.name : undefined"
            >
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span v-if="!collapsed">{{ item.name }}</span>
            </Link>

            <!-- View Site Button -->
            <a
                href="/"
                target="_blank"
                :class="[
                    'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-gray-800 text-white hover:bg-gray-700 transition-colors',
                    collapsed ? 'justify-center' : ''
                ]"
                :title="collapsed ? 'View Site' : undefined"
            >
                <svg class="h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                </svg>
                <span v-if="!collapsed">View Site</span>
            </a>
        </div>
    </aside>
</template>
