<script setup lang="ts">
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import ApplicationIcon from '@/Components/ApplicationIcon.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Sidebar from '@/Components/Admin/Sidebar.vue';

const showMobileSidebar = ref(false);
const sidebarCollapsed = ref(false);

const onSidebarCollapse = (collapsed: boolean) => {
    sidebarCollapsed.value = collapsed;
};
</script>

<template>
    <div>
        <!-- Skip Link for Accessibility -->
        <a
            href="#main-content"
            class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-gray-800 focus:text-white focus:rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700"
        >
            Skip to main content
        </a>

        <div class="min-h-screen bg-gray-100">
            <!-- Desktop Sidebar -->
            <div class="hidden lg:block">
                <Sidebar @update:collapsed="onSidebarCollapse" />
            </div>

            <!-- Mobile Sidebar Overlay -->
            <div
                v-if="showMobileSidebar"
                class="fixed inset-0 z-40 lg:hidden"
            >
                <!-- Backdrop -->
                <div
                    class="fixed inset-0 bg-gray-600/75"
                    @click="showMobileSidebar = false"
                ></div>

                <!-- Sidebar -->
                <div class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900">
                    <!-- Close button -->
                    <div class="absolute top-4 right-4">
                        <button
                            @click="showMobileSidebar = false"
                            class="p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-800"
                        >
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Logo -->
                    <div class="flex h-16 items-center px-4 border-b border-gray-800">
                        <Link :href="route('admin.dashboard')" class="flex items-center gap-3" @click="showMobileSidebar = false">
                            <ApplicationIcon class="h-8 w-8 text-white" />
                            <span class="font-semibold text-white">Merik Studio</span>
                        </Link>
                    </div>

                    <!-- Navigation -->
                    <nav class="flex-1 overflow-y-auto py-4 px-3">
                        <!-- Main -->
                        <div class="mb-6">
                            <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Main</p>
                            <ul class="space-y-1">
                                <li>
                                    <Link
                                        :href="route('admin.dashboard')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.dashboard')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                        Dashboard
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Content</p>
                            <ul class="space-y-1">
                                <li>
                                    <Link
                                        :href="route('admin.pages.index')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.pages.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Pages
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        :href="route('admin.blog.posts.index')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.blog.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                                        </svg>
                                        Blog
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        :href="route('admin.products.index')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.products.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Products
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <!-- Marketing -->
                        <div class="mb-6">
                            <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Marketing</p>
                            <ul class="space-y-1">
                                <li>
                                    <Link
                                        :href="route('admin.calendar.index')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.calendar.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Calendar
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        :href="route('admin.campaigns.index')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.campaigns.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        Campaigns
                                    </Link>
                                </li>
                            </ul>
                        </div>

                        <!-- Admin -->
                        <div class="mb-6">
                            <p class="px-3 mb-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</p>
                            <ul class="space-y-1">
                                <li>
                                    <Link
                                        :href="route('admin.navigation.edit')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.navigation.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                        </svg>
                                        Navigation
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        :href="route('admin.users.index')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.users.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        Users
                                    </Link>
                                </li>
                                <li>
                                    <Link
                                        :href="route('admin.settings.edit')"
                                        :class="[
                                            'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                                            route().current('admin.settings.*')
                                                ? 'bg-gray-800 text-white'
                                                : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                                        ]"
                                        @click="showMobileSidebar = false"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Settings
                                    </Link>
                                </li>
                            </ul>
                        </div>
                    </nav>

                    <!-- Bottom Section -->
                    <div class="border-t border-gray-800 p-3">
                        <Link
                            :href="route('admin.help.index')"
                            :class="[
                                'flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium transition-colors mb-2',
                                route().current('admin.help.*')
                                    ? 'bg-gray-800 text-white'
                                    : 'text-gray-400 hover:bg-gray-800 hover:text-white'
                            ]"
                            @click="showMobileSidebar = false"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Help
                        </Link>
                        <a
                            href="/"
                            target="_blank"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-gray-800 text-white hover:bg-gray-700 transition-colors"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            View Site
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div
                :class="[
                    'transition-all duration-300',
                    sidebarCollapsed ? 'lg:pl-16' : 'lg:pl-64'
                ]"
            >
                <!-- Top Bar -->
                <div class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-white px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                    <!-- Mobile menu button -->
                    <button
                        type="button"
                        class="-m-2.5 p-2.5 text-gray-700 lg:hidden"
                        @click="showMobileSidebar = true"
                    >
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Separator -->
                    <div class="h-6 w-px bg-gray-200 lg:hidden" aria-hidden="true"></div>

                    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <div class="flex flex-1"></div>
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            <!-- User dropdown -->
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                                    >
                                        <span class="hidden sm:block">{{ $page.props.auth.user.name }}</span>
                                        <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')">
                                        Profile
                                    </DropdownLink>
                                    <DropdownLink
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                    >
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>
                </div>

                <!-- Page Heading -->
                <header
                    class="bg-white shadow"
                    v-if="$slots.header"
                >
                    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main id="main-content">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
