<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:shadow-black/20 border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-200">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Telemetry Logs
            </h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">
                A complete audit trail of all logic engine executions.
            </p>
        </div>
        <div v-if="isLoading" class="flex items-center space-x-3 text-gray-500 dark:text-gray-400 py-8 justify-center">
            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="font-medium tracking-tight">
                Loading telemetry data...
            </span>
        </div>
        <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-lg p-4 text-red-600 dark:text-red-400 text-sm font-medium">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.217a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clip-rule="evenodd" />
                </svg>
                <span>{{ error }}</span>
            </div>
        </div>
        <div v-else-if="!logs.length" class="text-center py-12 border-2 border-dashed border-gray-100 dark:border-gray-700/50 rounded-xl">
            <p class="text-gray-500 dark:text-gray-400 font-medium italic">
                No telemetry logs found.
            </p>
        </div>
        <div v-else class="space-y-6">
            <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wide sm:pl-6">
                            Hook
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wide">
                            Duration
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wide">
                            Causer
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wide">
                            Date
                        </th>
                        <th scope="col" class="px-3 py-3.5 text-left text-xs font-semibold text-gray-900 dark:text-gray-200 uppercase tracking-wide">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                    <tr v-for="log in logs" :key="log.id" class="hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                            <div class="font-mono font-medium text-fuchsia-600 dark:text-fuchsia-400">
                                {{ log.hook }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                {{ log.total_duration }}ms
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                            <span v-if="log.causer" class="inline-flex items-center">
                                {{ log.causer.email || log.causer.name || 'User #' + log.causer_id }}
                            </span>
                            <span v-else class="text-gray-400 italic dark:text-gray-500">
                                System / Guest
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-300">
                            {{ log.formatted_date }}
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            <router-link
                                :to="{ name: 'telemetry.show', params: { id: log.id } }"
                                class="flex items-center space-x-1 px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded transition-colors cursor-pointer"
                                title="View Traces"
                            >View Traces</router-link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="pagination.total > 0" class="bg-gray-50 dark:bg-gray-900/50 px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between sm:px-6 transition-colors">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Showing
                            <span class="font-medium">
                                {{ pagination.from }}
                            </span>
                            to
                            <span class="font-medium">
                                {{ pagination.to }}
                            </span>
                            of
                            <span class="font-medium">
                                {{ pagination.total }}
                            </span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                        aria-label="Pagination">
                            <template v-for="(link, index) in pagination.links" :key="index">
                                <button
                                    @click="goToPage(link)"
                                    :disabled="!link.url"
                                    v-html="link.label"
                                    :class="[
                                        'relative inline-flex items-center px-4 py-2 border text-sm font-medium transition-colors focus:z-20',
                                        link.active
                                        ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 dark:bg-indigo-900/50 dark:border-indigo-500 dark:text-indigo-400 cursor-default'
                                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:bg-gray-700',
                                        index === 0 ? 'rounded-l-md' : '',
                                        index === pagination.links.length - 1 ? 'rounded-r-md' : '',
                                        !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                                    ]"
                                ></button>
                            </template>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useApi } from '@/composables/useApi';

const api = useApi();
const logs = ref([]);
const pagination = ref({});
const isLoading = ref(false);
const error = ref(null);

const fetchLogs = async (page = 1) => {
    isLoading.value = true;
    error.value = null;
    try {
        const apiResp = await api.get('telemetry', { page });
        logs.value = apiResp.data.data;
        pagination.value = {
            current_page: apiResp.data.current_page,
            last_page: apiResp.data.last_page,
            prev_page_url: apiResp.data.prev_page_url,
            next_page_url: apiResp.data.next_page_url,
            from: apiResp.data.from,
            to: apiResp.data.to,
            total: apiResp.data.total,
            links: apiResp.data.links
        };
    } catch(err) {
        error.value = err || 'Failed to load telemetry logs.';
        console.log(err);
    } finally {
        isLoading.value = false;
    }
};

const goToPage = (link) => {
    if (!link) return;
    if (link.page) fetchLogs(link.page);
};

onMounted(() => {
    fetchLogs();
});
</script>
