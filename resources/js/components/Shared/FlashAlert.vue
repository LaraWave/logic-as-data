<template>
    <transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-6"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="flash.isVisible" 
            class="fixed bottom-6 right-6 z-[100] max-w-sm w-full shadow-2xl rounded-xl border-none pointer-events-auto overflow-hidden"
            :class="activeStyle.wrapper"
        >
            <div class="p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <component :is="activeStyle.icon" class="h-6 w-6 text-white" />
                    </div>

                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-bold text-white leading-tight">
                            {{ flash.message }}
                        </p>
                    </div>

                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="flash.hide()" class="rounded-lg p-1 text-white/80 hover:text-white hover:bg-white/20 transition-all focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { computed, h } from 'vue';
import { flash } from '@/services/flash';

const Icons = {
    success: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' })]),
    danger: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })]),
    warning: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' })]),
    info: () => h('svg', { fill: 'none', viewBox: '0 0 24 24', stroke: 'currentColor' }, [h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' })]),
};

const styles = {
    success: {
        wrapper: 'bg-emerald-600 dark:bg-emerald-500',
        icon: Icons.success,
    },
    danger: {
        wrapper: 'bg-red-600 dark:bg-red-500',
        icon: Icons.danger,
    },
    warning: {
        wrapper: 'bg-amber-500 dark:bg-amber-600',
        icon: Icons.warning,
    },
    info: {
        wrapper: 'bg-blue-600 dark:bg-blue-500',
        icon: Icons.info,
    }
};

const activeStyle = computed(() => styles[flash.type] || styles.info);
</script>
