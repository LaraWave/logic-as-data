<template>
    <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 py-4 px-8 sticky top-0 z-20 transition-colors duration-300">
        <div class="flex justify-between items-center">
            <h1 class="text-sm font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                {{ $route.meta.title || 'Dashboard' }}
            </h1>

            <div class="flex items-center space-x-4">
                <button
                    @click="toggleTheme" 
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:text-indigo-600 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 cursor-pointer"
                    :aria-label="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                >
                    <span v-if="isDark" role="img" aria-label="Sun">☀️</span>
                    <span v-else role="img" aria-label="Moon">🌙</span>
                </button>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const isDark = ref(false);

const applyTheme = () => {
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('logic-as-data-theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('logic-as-data-theme', 'light');
    }
};

const toggleTheme = () => {
    isDark.value = !isDark.value;
    applyTheme();
};

onMounted(() => {
    const saved = localStorage.getItem('logic-as-data-theme');

    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    }
});
</script>