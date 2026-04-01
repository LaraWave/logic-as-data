<template>
    <aside class="w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 flex flex-col h-screen sticky top-0 transition-colors">
        <div class="p-6">
            <div class="flex items-center gap-x-3 px-2 py-2 group cursor-pointer">
                <div class="flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-50/50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-500/30 transition-all duration-300">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="5" cy="12" r="2" fill="currentColor" stroke="none" />
                        
                        <path d="M7 12h3l4-5h2" />
                        <circle cx="18" cy="7" r="2" fill="currentColor" stroke="none" />
                        
                        <g class="opacity-40 group-hover:opacity-60 transition-opacity">
                            <path d="M10 12l4 5h2" />
                            <circle cx="18" cy="17" r="2" fill="currentColor" stroke="none" />
                        </g>
                    </svg>
                </div>
                <div class="font-bold text-xl tracking-tight flex items-baseline select-none">
                    <span class="text-gray-900 dark:text-white">Logic</span>
                    <span class="text-gray-400 dark:text-gray-500 mx-0.5 text-[15px] font-medium">As</span>
                    <span class="text-indigo-600 dark:text-indigo-400">Data</span>
                </div>
            </div>
        </div>

        <nav class="flex-1 px-4 py-2">
            <ul class="space-y-1">
                <li v-for="item in menuItems" :key="item.path">
                    <router-link
                        :to="{ name: item.routeName }"
                        class="flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-colors"
                        active-class="bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400"
                        inactive-class="text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-200"
                    >
                        {{ item.name }}
                    </router-link>
                </li>
            </ul>
        </nav>
    </aside>
</template>

<script setup>
import { useConfig } from '@/composables/useConfig';

const { routePrefix } = useConfig();

const menuItems = [
    { name: 'Logic Rules', routeName: 'logic-rules.index' }
];
</script>
