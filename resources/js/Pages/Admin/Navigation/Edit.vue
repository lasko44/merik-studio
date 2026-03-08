<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

interface NavItem {
    id: number | 'blog' | 'products';
    title: string;
    path: string;
    is_published: boolean;
    show_in_nav: boolean;
    parent_id: number | null;
    nav_order: number;
    children: NavItem[];
    is_blog?: boolean;
    is_products?: boolean;
}

interface AvailablePage {
    id: number;
    title: string;
    path: string;
    is_published: boolean;
    show_in_nav: boolean;
    parent_id: number | null;
    nav_order: number;
}

interface BlogSettings {
    showInNav: boolean;
    navLabel: string;
    navOrder: number;
}

interface ProductSettings {
    showInNav: boolean;
    navLabel: string;
    navOrder: number;
}

interface Props {
    navItems: NavItem[];
    availablePages: AvailablePage[];
    blogSettings: BlogSettings;
    productSettings: ProductSettings;
}

const props = defineProps<Props>();

// Local state
const navItems = ref<NavItem[]>(JSON.parse(JSON.stringify(props.navItems)));
const availablePages = ref<AvailablePage[]>(JSON.parse(JSON.stringify(props.availablePages)));
const isSaving = ref(false);
const hasChanges = ref(false);

// Drag state
const draggedItemId = ref<number | 'blog' | 'products' | null>(null);
const draggedSource = ref<'nav' | 'available' | null>(null);
const dropTargetId = ref<number | 'blog' | 'products' | null>(null);
const dropPosition = ref<'before' | 'after' | 'inside' | null>(null);

// Visual feedback for arrow moves
const recentlyMovedId = ref<number | 'blog' | 'products' | null>(null);
let moveTimeout: ReturnType<typeof setTimeout> | null = null;

const flashMoved = (id: number | 'blog' | 'products') => {
    if (moveTimeout) clearTimeout(moveTimeout);
    recentlyMovedId.value = id;
    moveTimeout = setTimeout(() => {
        recentlyMovedId.value = null;
    }, 600);
};

// Check if item is a special nav item (blog, products)
const isSpecialItem = (id: number | 'blog' | 'products'): boolean => {
    return id === 'blog' || id === 'products';
};

// Flatten nav items for saving
const flattenNavItems = (items: NavItem[], parentId: number | null = null): any[] => {
    const result: any[] = [];
    items.forEach((item, index) => {
        result.push({
            id: item.id,
            parent_id: isSpecialItem(item.id) ? null : parentId, // Special items can't be nested
            nav_order: index,
            show_in_nav: true,
        });
        // Special items can't have children, but regular pages can
        if (!isSpecialItem(item.id) && item.children?.length > 0) {
            result.push(...flattenNavItems(item.children, item.id as number));
        }
    });
    return result;
};

// Save navigation structure
const save = () => {
    isSaving.value = true;

    const items = flattenNavItems(navItems.value);

    availablePages.value.forEach((page, index) => {
        items.push({
            id: page.id,
            parent_id: null,
            nav_order: index,
            show_in_nav: false,
        });
    });

    router.patch(route('admin.navigation.update'), { items }, {
        preserveScroll: true,
        onSuccess: () => {
            hasChanges.value = false;
        },
        onFinish: () => {
            isSaving.value = false;
        },
    });
};

// Find and remove item from nav tree (returns removed item)
const removeFromNav = (items: NavItem[], id: number | 'blog' | 'products'): NavItem | null => {
    for (let i = 0; i < items.length; i++) {
        if (items[i].id === id) {
            return items.splice(i, 1)[0];
        }
        if (items[i].children?.length > 0) {
            const found = removeFromNav(items[i].children, id);
            if (found) return found;
        }
    }
    return null;
};

// Insert item into nav tree at specific position
const insertIntoNav = (items: NavItem[], targetId: number | 'blog' | 'products', item: NavItem, position: 'before' | 'after' | 'inside'): boolean => {
    for (let i = 0; i < items.length; i++) {
        if (items[i].id === targetId) {
            if (position === 'inside') {
                // Can't nest inside special items, and special items can't have children
                if (isSpecialItem(items[i].id) || isSpecialItem(item.id)) {
                    // Fall back to placing after instead
                    items.splice(i + 1, 0, item);
                } else {
                    if (!items[i].children) items[i].children = [];
                    items[i].children.push(item);
                }
            } else if (position === 'before') {
                items.splice(i, 0, item);
            } else {
                items.splice(i + 1, 0, item);
            }
            return true;
        }
        if (items[i].children?.length > 0) {
            if (insertIntoNav(items[i].children, targetId, item, position)) return true;
        }
    }
    return false;
};

// Drag start
const onDragStart = (e: DragEvent, id: number | 'blog' | 'products', source: 'nav' | 'available') => {
    draggedItemId.value = id;
    draggedSource.value = source;
    e.dataTransfer!.effectAllowed = 'move';
    e.dataTransfer!.setData('text/plain', String(id));
};

// Drag end - reset state
const onDragEnd = () => {
    draggedItemId.value = null;
    draggedSource.value = null;
    dropTargetId.value = null;
    dropPosition.value = null;
};

// Handle drop on a nav item
const handleDrop = (targetId: number | 'blog' | 'products', position: 'before' | 'after' | 'inside') => {
    if (!draggedItemId.value || !draggedSource.value) return;

    // Don't allow dropping on self
    if (draggedItemId.value === targetId) return;

    let item: NavItem;

    if (draggedSource.value === 'available') {
        const index = availablePages.value.findIndex(p => p.id === draggedItemId.value);
        if (index === -1) return;
        const page = availablePages.value.splice(index, 1)[0];
        item = { ...page, children: [], show_in_nav: true };
    } else {
        const removed = removeFromNav(navItems.value, draggedItemId.value);
        if (!removed) return;
        item = removed;
    }

    const inserted = insertIntoNav(navItems.value, targetId, item, position);

    // If insert failed (shouldn't happen), add to end
    if (!inserted) {
        navItems.value.push(item);
    }

    hasChanges.value = true;
    onDragEnd();
};

// Handle drop on nav container (add to end of root)
const handleDropOnContainer = () => {
    if (!draggedItemId.value || !draggedSource.value) return;

    if (draggedSource.value === 'available') {
        const index = availablePages.value.findIndex(p => p.id === draggedItemId.value);
        if (index === -1) return;
        const page = availablePages.value.splice(index, 1)[0];
        navItems.value.push({ ...page, children: [], show_in_nav: true });
    } else {
        const removed = removeFromNav(navItems.value, draggedItemId.value);
        if (removed) {
            navItems.value.push(removed);
        }
    }

    hasChanges.value = true;
    onDragEnd();
};

// Handle drop on available container
const handleDropOnAvailable = () => {
    if (!draggedItemId.value || draggedSource.value !== 'nav') return;

    const removed = removeFromNav(navItems.value, draggedItemId.value);
    if (removed) {
        // Special items (blog, products) don't go to available pages - just remove from nav
        if (isSpecialItem(removed.id)) {
            hasChanges.value = true;
            onDragEnd();
            return;
        }

        const flattenItem = (item: NavItem): AvailablePage[] => {
            // Skip special items when flattening
            if (isSpecialItem(item.id)) return [];

            const result: AvailablePage[] = [{
                id: item.id as number,
                title: item.title,
                path: item.path,
                is_published: item.is_published,
                show_in_nav: false,
                parent_id: null,
                nav_order: 0,
            }];
            if (item.children?.length > 0) {
                item.children.forEach(child => result.push(...flattenItem(child)));
            }
            return result;
        };
        availablePages.value.push(...flattenItem(removed));
        hasChanges.value = true;
    }
    onDragEnd();
};

// Move item up
const moveUp = (id: number | 'blog' | 'products') => {
    const moveInArray = (items: NavItem[]): boolean => {
        for (let i = 0; i < items.length; i++) {
            if (items[i].id === id && i > 0) {
                [items[i - 1], items[i]] = [items[i], items[i - 1]];
                return true;
            }
            if (items[i].children?.length > 0 && moveInArray(items[i].children)) {
                return true;
            }
        }
        return false;
    };
    if (moveInArray(navItems.value)) {
        hasChanges.value = true;
        flashMoved(id);
    }
};

// Move item down
const moveDown = (id: number | 'blog' | 'products') => {
    const moveInArray = (items: NavItem[]): boolean => {
        for (let i = 0; i < items.length; i++) {
            if (items[i].id === id && i < items.length - 1) {
                [items[i], items[i + 1]] = [items[i + 1], items[i]];
                return true;
            }
            if (items[i].children?.length > 0 && moveInArray(items[i].children)) {
                return true;
            }
        }
        return false;
    };
    if (moveInArray(navItems.value)) {
        hasChanges.value = true;
        flashMoved(id);
    }
};

// Remove from nav
const removeFromNavigation = (id: number | 'blog' | 'products') => {
    const removed = removeFromNav(navItems.value, id);
    if (removed) {
        // Special items (blog, products) don't go to available pages - they're controlled by settings
        if (isSpecialItem(removed.id)) {
            hasChanges.value = true;
            return;
        }

        const flattenItem = (item: NavItem): AvailablePage[] => {
            // Skip special items when flattening
            if (isSpecialItem(item.id)) return [];

            const result: AvailablePage[] = [{
                id: item.id as number,
                title: item.title,
                path: item.path,
                is_published: item.is_published,
                show_in_nav: false,
                parent_id: null,
                nav_order: 0,
            }];
            if (item.children?.length > 0) {
                item.children.forEach(child => result.push(...flattenItem(child)));
            }
            return result;
        };
        availablePages.value.push(...flattenItem(removed));
        hasChanges.value = true;
    }
};

// Add to nav
const addToNavigation = (page: AvailablePage) => {
    const index = availablePages.value.findIndex(p => p.id === page.id);
    if (index !== -1) {
        availablePages.value.splice(index, 1);
        navItems.value.push({
            ...page,
            children: [],
            show_in_nav: true,
        });
        hasChanges.value = true;
    }
};

// Check if this is the current drop target
const isDropTarget = (id: number | 'blog' | 'products', pos: 'before' | 'after' | 'inside') => {
    return dropTargetId.value === id && dropPosition.value === pos;
};
</script>

<template>
    <Head title="Navigation Editor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Navigation Editor
                    </h2>
                    <p class="text-sm text-gray-500">Drag and drop pages to organize your site navigation</p>
                </div>
                <div class="flex items-center gap-3">
                    <span v-if="hasChanges" class="text-sm text-amber-600">Unsaved changes</span>
                    <PrimaryButton @click="save" :disabled="isSaving || !hasChanges">
                        {{ isSaving ? 'Saving...' : 'Save Navigation' }}
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Flash Messages -->
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-lg bg-green-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <p class="ml-3 text-sm text-green-700">{{ $page.props.flash.success }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Navigation Structure -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Navigation Menu</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Drag pages to reorder. Use the arrow buttons or drag onto another item to nest.
                        </p>

                        <div
                            class="min-h-[300px] border-2 border-dashed rounded-lg p-4 transition-colors"
                            :class="draggedItemId && !dropTargetId ? 'border-indigo-300 bg-indigo-50' : 'border-gray-200'"
                            @dragover.prevent
                            @drop.prevent="handleDropOnContainer"
                        >
                            <template v-if="navItems.length > 0">
                                <template v-for="(item, index) in navItems" :key="item.id">
                                    <NavItemRow
                                        :item="item"
                                        :depth="0"
                                        :dragged-item-id="draggedItemId"
                                        :drop-target-id="dropTargetId"
                                        :drop-position="dropPosition"
                                        :recently-moved-id="recentlyMovedId"
                                        @drag-start="onDragStart"
                                        @drag-end="onDragEnd"
                                        @set-drop-target="(id: number | 'blog' | 'products', pos: 'before' | 'after' | 'inside') => { dropTargetId = id; dropPosition = pos; }"
                                        @clear-drop-target="dropTargetId = null; dropPosition = null"
                                        @drop="handleDrop"
                                        @move-up="moveUp"
                                        @move-down="moveDown"
                                        @remove="removeFromNavigation"
                                    />
                                </template>
                            </template>
                            <div v-else class="text-center py-12 text-gray-400">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                <p class="mt-2">Drag pages here to add them to navigation</p>
                            </div>
                        </div>
                    </div>

                    <!-- Available Pages -->
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Available Pages</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Pages not currently in the navigation menu.
                        </p>

                        <div
                            class="min-h-[300px] border-2 border-dashed rounded-lg p-4 transition-colors"
                            :class="draggedSource === 'nav' ? 'border-red-300 bg-red-50' : 'border-gray-200'"
                            @dragover.prevent
                            @drop.prevent="handleDropOnAvailable"
                        >
                            <template v-if="availablePages.length > 0">
                                <div
                                    v-for="page in availablePages"
                                    :key="page.id"
                                    class="flex items-center justify-between p-3 mb-2 bg-gray-50 rounded-lg border border-gray-200 cursor-grab hover:bg-gray-100 active:cursor-grabbing"
                                    draggable="true"
                                    @dragstart="onDragStart($event, page.id, 'available')"
                                    @dragend="onDragEnd"
                                >
                                    <div class="flex items-center gap-3">
                                        <svg class="h-5 w-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" />
                                        </svg>
                                        <div class="min-w-0">
                                            <div class="font-medium text-gray-900 truncate">{{ page.title }}</div>
                                            <div class="text-xs text-gray-500 truncate">{{ page.path }}</div>
                                        </div>
                                        <span
                                            v-if="!page.is_published"
                                            class="inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 flex-shrink-0"
                                        >
                                            Draft
                                        </span>
                                    </div>
                                    <button
                                        @click="addToNavigation(page)"
                                        class="p-1 text-gray-700 hover:text-gray-600 flex-shrink-0"
                                        title="Add to navigation"
                                    >
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </template>
                            <div v-else class="text-center py-12 text-gray-400">
                                <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="mt-2">All pages are in navigation</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script lang="ts">
import { defineComponent, h, PropType } from 'vue';

interface NavItemType {
    id: number | 'blog' | 'products';
    title: string;
    path: string;
    is_published: boolean;
    children: NavItemType[];
    is_blog?: boolean;
    is_products?: boolean;
}

const NavItemRow: ReturnType<typeof defineComponent> = defineComponent({
    name: 'NavItemRow',
    props: {
        item: { type: Object as PropType<NavItemType>, required: true },
        depth: { type: Number, default: 0 },
        draggedItemId: { type: [Number, String] as PropType<number | string | null>, default: null },
        dropTargetId: { type: [Number, String] as PropType<number | string | null>, default: null },
        dropPosition: { type: String as PropType<string | null>, default: null },
        recentlyMovedId: { type: [Number, String] as PropType<number | string | null>, default: null },
    },
    emits: ['drag-start', 'drag-end', 'set-drop-target', 'clear-drop-target', 'drop', 'move-up', 'move-down', 'remove'],
    setup(props, { emit }): () => ReturnType<typeof h> {
        const isBeingDragged = (): boolean => props.draggedItemId === props.item.id;
        const isDropTarget = (pos: string): boolean => props.dropTargetId === props.item.id && props.dropPosition === pos;
        const wasRecentlyMoved = (): boolean => props.recentlyMovedId === props.item.id;
        const isBlogItem = (): boolean => props.item.id === 'blog';
        const isProductsItem = (): boolean => props.item.id === 'products';
        const isSpecialItem = (): boolean => isBlogItem() || isProductsItem();

        return (): ReturnType<typeof h> => h('div', {
            class: ['nav-item-wrapper', isBeingDragged() ? 'opacity-50' : ''],
            style: { paddingLeft: `${props.depth * 20}px` },
        }, [
            // Drop zone BEFORE this item
            h('div', {
                class: [
                    'h-2 rounded-full mx-2 mb-1 transition-all duration-150',
                    isDropTarget('before') ? 'bg-gray-700' : 'bg-transparent',
                ],
                onDragover: (e: DragEvent) => {
                    e.preventDefault();
                    e.stopPropagation();
                    emit('set-drop-target', props.item.id, 'before');
                },
                onDragleave: (e: DragEvent) => {
                    e.stopPropagation();
                    emit('clear-drop-target');
                },
                onDrop: (e: DragEvent) => {
                    e.preventDefault();
                    e.stopPropagation();
                    emit('drop', props.item.id, 'before');
                },
            }),

            // The actual item row
            h('div', {
                class: [
                    'flex items-center justify-between p-3 mb-1 rounded-lg border cursor-grab active:cursor-grabbing transition-all duration-150',
                    wasRecentlyMoved()
                        ? 'bg-green-50 border-green-400 ring-2 ring-green-400'
                        : isDropTarget('inside') && !isSpecialItem()
                            ? 'bg-indigo-100 border-indigo-400 ring-2 ring-indigo-400'
                            : isBlogItem()
                                ? 'bg-purple-50 border-purple-200 hover:border-purple-300'
                                : isProductsItem()
                                    ? 'bg-emerald-50 border-emerald-200 hover:border-emerald-300'
                                    : 'bg-white border-gray-200 hover:border-gray-300',
                ],
                draggable: true,
                onDragstart: (e: DragEvent) => {
                    e.stopPropagation();
                    emit('drag-start', e, props.item.id, 'nav');
                },
                onDragend: (e: DragEvent) => {
                    e.stopPropagation();
                    emit('drag-end');
                },
                onDragover: (e: DragEvent) => {
                    e.preventDefault();
                    e.stopPropagation();
                    // Special items (blog, products) can't have children, so treat as 'after' instead of 'inside'
                    emit('set-drop-target', props.item.id, isSpecialItem() ? 'after' : 'inside');
                },
                onDragleave: (e: DragEvent) => {
                    e.stopPropagation();
                    emit('clear-drop-target');
                },
                onDrop: (e: DragEvent) => {
                    e.preventDefault();
                    e.stopPropagation();
                    // Special items (blog, products) can't have children, so treat as 'after' instead of 'inside'
                    emit('drop', props.item.id, isSpecialItem() ? 'after' : 'inside');
                },
            }, [
                // Left side - drag handle and info
                h('div', { class: 'flex items-center gap-3 min-w-0 flex-1' }, [
                    // Drag handle
                    h('svg', {
                        class: 'h-5 w-5 text-gray-400 flex-shrink-0',
                        fill: 'none',
                        stroke: 'currentColor',
                        viewBox: '0 0 24 24'
                    }, [
                        h('path', {
                            'stroke-linecap': 'round',
                            'stroke-linejoin': 'round',
                            'stroke-width': '2',
                            d: 'M4 8h16M4 16h16'
                        }),
                    ]),
                    // Depth indicator
                    ...(props.depth > 0 ? [
                        h('span', { class: 'text-gray-300 flex-shrink-0' }, '↳'),
                    ] : []),
                    // Title and path
                    h('div', { class: 'min-w-0 flex-1' }, [
                        h('div', { class: 'font-medium text-gray-900 truncate' }, props.item.title),
                        h('div', { class: 'text-xs text-gray-500 truncate' }, props.item.path),
                    ]),
                    // Blog badge
                    ...(isBlogItem() ? [
                        h('span', {
                            class: 'inline-flex items-center rounded-full bg-purple-100 px-2 py-0.5 text-xs font-medium text-purple-800 flex-shrink-0'
                        }, 'Blog'),
                    ] : []),
                    // Products badge
                    ...(isProductsItem() ? [
                        h('span', {
                            class: 'inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-medium text-emerald-800 flex-shrink-0'
                        }, 'Products'),
                    ] : []),
                    // Draft badge
                    ...(!props.item.is_published && !isSpecialItem() ? [
                        h('span', {
                            class: 'inline-flex items-center rounded-full bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800 flex-shrink-0'
                        }, 'Draft'),
                    ] : []),
                ]),
                // Right side - action buttons
                h('div', { class: 'flex items-center gap-1 flex-shrink-0 ml-2' }, [
                    // Move up button
                    h('button', {
                        class: 'p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded',
                        title: 'Move up',
                        onClick: (e: Event) => {
                            e.preventDefault();
                            e.stopPropagation();
                            emit('move-up', props.item.id);
                        },
                    }, [
                        h('svg', { class: 'h-4 w-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M5 15l7-7 7 7' }),
                        ]),
                    ]),
                    // Move down button
                    h('button', {
                        class: 'p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded',
                        title: 'Move down',
                        onClick: (e: Event) => {
                            e.preventDefault();
                            e.stopPropagation();
                            emit('move-down', props.item.id);
                        },
                    }, [
                        h('svg', { class: 'h-4 w-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M19 9l-7 7-7-7' }),
                        ]),
                    ]),
                    // Remove button
                    h('button', {
                        class: 'p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded',
                        title: 'Remove from navigation',
                        onClick: (e: Event) => {
                            e.preventDefault();
                            e.stopPropagation();
                            emit('remove', props.item.id);
                        },
                    }, [
                        h('svg', { class: 'h-4 w-4', fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
                            h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M6 18L18 6M6 6l12 12' }),
                        ]),
                    ]),
                ]),
            ]),

            // Drop zone AFTER this item (only for last item or when no children)
            h('div', {
                class: [
                    'h-2 rounded-full mx-2 mb-1 transition-all duration-150',
                    isDropTarget('after') ? 'bg-gray-700' : 'bg-transparent',
                ],
                onDragover: (e: DragEvent) => {
                    e.preventDefault();
                    e.stopPropagation();
                    emit('set-drop-target', props.item.id, 'after');
                },
                onDragleave: (e: DragEvent) => {
                    e.stopPropagation();
                    emit('clear-drop-target');
                },
                onDrop: (e: DragEvent) => {
                    e.preventDefault();
                    e.stopPropagation();
                    emit('drop', props.item.id, 'after');
                },
            }),

            // Render children recursively
            ...(props.item.children?.length > 0 ? props.item.children.map((child: NavItemType): ReturnType<typeof h> =>
                h(NavItemRow, {
                    key: child.id,
                    item: child,
                    depth: props.depth + 1,
                    draggedItemId: props.draggedItemId,
                    dropTargetId: props.dropTargetId,
                    dropPosition: props.dropPosition,
                    recentlyMovedId: props.recentlyMovedId,
                    onDragStart: (e: DragEvent, id: number | 'blog' | 'products', source: string) => emit('drag-start', e, id, source),
                    onDragEnd: () => emit('drag-end'),
                    onSetDropTarget: (id: number | 'blog' | 'products', pos: string) => emit('set-drop-target', id, pos),
                    onClearDropTarget: () => emit('clear-drop-target'),
                    onDrop: (id: number | 'blog' | 'products', pos: string) => emit('drop', id, pos),
                    onMoveUp: (id: number | 'blog' | 'products') => emit('move-up', id),
                    onMoveDown: (id: number | 'blog' | 'products') => emit('move-down', id),
                    onRemove: (id: number | 'blog' | 'products') => emit('remove', id),
                })
            ) : []),
        ]);
    },
});

export default {
    components: { NavItemRow },
};
</script>
