<script setup lang="ts">
interface Props {
    template: string;
}

defineProps<Props>();
</script>

<template>
    <svg viewBox="0 0 120 90" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-full">
        <!-- Default Template -->
        <template v-if="template === 'default'">
            <!-- Container outline -->
            <rect x="15" y="5" width="90" height="80" rx="2" fill="#F3F4F6" stroke="#D1D5DB" stroke-width="1" />
            <!-- Content blocks -->
            <rect x="20" y="10" width="80" height="10" rx="1" fill="#9CA3AF" />
            <rect x="20" y="25" width="80" height="20" rx="1" fill="#E5E7EB" />
            <rect x="20" y="50" width="80" height="30" rx="1" fill="#E5E7EB" />
        </template>

        <!-- Full Width Template -->
        <template v-else-if="template === 'full-width'">
            <!-- Full width hero -->
            <rect x="5" y="5" width="110" height="25" rx="2" fill="#818CF8" />
            <!-- Full width sections -->
            <rect x="5" y="35" width="110" height="20" rx="1" fill="#E5E7EB" />
            <rect x="5" y="60" width="110" height="25" rx="1" fill="#F3F4F6" />
        </template>

        <!-- Sidebar Right Template -->
        <template v-else-if="template === 'sidebar'">
            <!-- Container -->
            <rect x="10" y="5" width="100" height="80" rx="2" fill="#F3F4F6" stroke="#D1D5DB" stroke-width="1" />
            <!-- Main content (2/3) -->
            <rect x="15" y="10" width="60" height="70" rx="1" fill="#E5E7EB" />
            <!-- Sidebar (1/3) -->
            <rect x="80" y="10" width="25" height="25" rx="1" fill="#C4B5FD" />
            <rect x="80" y="40" width="25" height="15" rx="1" fill="#DDD6FE" />
            <rect x="80" y="60" width="25" height="15" rx="1" fill="#DDD6FE" />
        </template>

        <!-- Sidebar Left Template -->
        <template v-else-if="template === 'sidebar-left'">
            <!-- Container -->
            <rect x="10" y="5" width="100" height="80" rx="2" fill="#F3F4F6" stroke="#D1D5DB" stroke-width="1" />
            <!-- Sidebar (1/3) -->
            <rect x="15" y="10" width="25" height="25" rx="1" fill="#C4B5FD" />
            <rect x="15" y="40" width="25" height="15" rx="1" fill="#DDD6FE" />
            <rect x="15" y="60" width="25" height="15" rx="1" fill="#DDD6FE" />
            <!-- Main content (2/3) -->
            <rect x="45" y="10" width="60" height="70" rx="1" fill="#E5E7EB" />
        </template>

        <!-- Landing Template -->
        <template v-else-if="template === 'landing'">
            <!-- Full width hero -->
            <rect x="5" y="5" width="110" height="30" rx="2" fill="#818CF8" />
            <!-- Contained content sections -->
            <rect x="15" y="40" width="90" height="15" rx="1" fill="#E5E7EB" />
            <rect x="15" y="60" width="90" height="25" rx="1" fill="#F3F4F6" />
        </template>

        <!-- Blog Template -->
        <template v-else-if="template === 'blog'">
            <!-- Narrow container centered -->
            <rect x="25" y="5" width="70" height="80" rx="2" fill="#F3F4F6" stroke="#D1D5DB" stroke-width="1" />
            <!-- Title -->
            <rect x="30" y="10" width="60" height="8" rx="1" fill="#9CA3AF" />
            <!-- Content lines -->
            <rect x="30" y="23" width="60" height="4" rx="1" fill="#E5E7EB" />
            <rect x="30" y="30" width="55" height="4" rx="1" fill="#E5E7EB" />
            <rect x="30" y="37" width="58" height="4" rx="1" fill="#E5E7EB" />
            <rect x="30" y="44" width="50" height="4" rx="1" fill="#E5E7EB" />
            <rect x="30" y="53" width="60" height="4" rx="1" fill="#E5E7EB" />
            <rect x="30" y="60" width="54" height="4" rx="1" fill="#E5E7EB" />
            <rect x="30" y="67" width="57" height="4" rx="1" fill="#E5E7EB" />
            <rect x="30" y="74" width="45" height="4" rx="1" fill="#E5E7EB" />
        </template>

        <!-- Portfolio Template -->
        <template v-else-if="template === 'portfolio'">
            <!-- Container -->
            <rect x="10" y="5" width="100" height="80" rx="2" fill="#F3F4F6" stroke="#D1D5DB" stroke-width="1" />
            <!-- Grid items -->
            <rect x="15" y="10" width="28" height="22" rx="1" fill="#A5B4FC" />
            <rect x="46" y="10" width="28" height="22" rx="1" fill="#C4B5FD" />
            <rect x="77" y="10" width="28" height="22" rx="1" fill="#818CF8" />
            <rect x="15" y="37" width="28" height="22" rx="1" fill="#C4B5FD" />
            <rect x="46" y="37" width="28" height="22" rx="1" fill="#818CF8" />
            <rect x="77" y="37" width="28" height="22" rx="1" fill="#A5B4FC" />
            <rect x="15" y="64" width="28" height="16" rx="1" fill="#818CF8" />
            <rect x="46" y="64" width="28" height="16" rx="1" fill="#A5B4FC" />
            <rect x="77" y="64" width="28" height="16" rx="1" fill="#C4B5FD" />
        </template>

        <!-- Documentation Template -->
        <template v-else-if="template === 'documentation'">
            <!-- Container -->
            <rect x="5" y="5" width="110" height="80" rx="2" fill="#F3F4F6" stroke="#D1D5DB" stroke-width="1" />
            <!-- Left nav -->
            <rect x="10" y="10" width="20" height="70" rx="1" fill="#DDD6FE" />
            <rect x="12" y="15" width="16" height="3" rx="0.5" fill="#A78BFA" />
            <rect x="12" y="21" width="14" height="3" rx="0.5" fill="#C4B5FD" />
            <rect x="12" y="27" width="15" height="3" rx="0.5" fill="#C4B5FD" />
            <rect x="12" y="33" width="13" height="3" rx="0.5" fill="#C4B5FD" />
            <!-- Main content -->
            <rect x="35" y="10" width="50" height="70" rx="1" fill="#E5E7EB" />
            <!-- Right TOC -->
            <rect x="90" y="10" width="20" height="40" rx="1" fill="#F9FAFB" stroke="#E5E7EB" stroke-width="1" />
            <rect x="92" y="15" width="16" height="2" rx="0.5" fill="#D1D5DB" />
            <rect x="92" y="20" width="14" height="2" rx="0.5" fill="#E5E7EB" />
            <rect x="92" y="25" width="15" height="2" rx="0.5" fill="#E5E7EB" />
            <rect x="92" y="30" width="12" height="2" rx="0.5" fill="#E5E7EB" />
        </template>

        <!-- Centered Template -->
        <template v-else-if="template === 'centered'">
            <!-- Small centered container -->
            <rect x="30" y="15" width="60" height="60" rx="2" fill="#F3F4F6" stroke="#D1D5DB" stroke-width="1" />
            <!-- Form-like elements -->
            <rect x="38" y="22" width="44" height="6" rx="1" fill="#9CA3AF" />
            <rect x="38" y="32" width="44" height="8" rx="1" fill="#E5E7EB" />
            <rect x="38" y="44" width="44" height="8" rx="1" fill="#E5E7EB" />
            <rect x="38" y="56" width="20" height="8" rx="1" fill="#818CF8" />
        </template>

        <!-- Fallback -->
        <template v-else>
            <rect x="15" y="5" width="90" height="80" rx="2" fill="#F3F4F6" stroke="#D1D5DB" stroke-width="1" />
            <text x="60" y="50" text-anchor="middle" fill="#9CA3AF" font-size="10">{{ template }}</text>
        </template>
    </svg>
</template>
