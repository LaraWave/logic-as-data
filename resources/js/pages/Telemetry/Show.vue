<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-6">
            <button @click="router.back()" class="inline-flex items-center text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                <svg class="mr-1 h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Logs
            </button>
        </div>
        <div v-if="isLoading" class="py-12 flex justify-center">
            <svg class="animate-spin h-8 w-8 text-indigo-600 dark:text-indigo-400"
            fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
        </div>
        <div v-else-if="log" class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-700 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                            <span class="font-mono text-indigo-600 dark:text-indigo-400 mr-3">
                                {{ log.hook }}
                            </span>
                        </h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            ID: {{ log.id }} - {{ log.formatted_date }}
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                            Total Duration:
                            <span class="font-bold ml-1 text-fuchsia-600 dark:text-fuchsia-400">
                                {{ log.total_duration }}ms
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Request Details
                        </h2>
                        <dl class="space-y-4 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider mb-1">
                                    Causer
                                </dt>
                                <dd class="text-gray-900 dark:text-gray-100 font-medium flex items-center">
                                    <template v-if="log.causer">
                                        <svg class="mr-2 h-6 w-6 text-gray-400" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        <span>
                                            {{ log.causer.email || log.causer.name || `User #${log.causer_id}` }}
                                            <span class="block text-xs font-normal text-gray-500 dark:text-gray-400 mt-0.5">
                                                {{ log.causer_type }}
                                            </span>
                                        </span>
                                    </template>
                                    <span v-else class="italic text-gray-500 flex items-center">
                                        <svg class="mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M12 5l7 7-7 7" />
                                        </svg>
                                        System / Guest
                                    </span>
                                </dd>
                            </div>
                            <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                <dt class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider mb-1">
                                    Session ID
                                </dt>
                                <dd class="font-mono text-xs text-gray-800 dark:text-gray-300 break-all">
                                    {{ log.session_id || '-' }}
                                </dd>
                            </div>
                            <div class="pt-3 border-t border-gray-100 dark:border-gray-700">
                                <dt class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wider mb-1">
                                    Request ID
                                </dt>
                                <dd class="font-mono text-xs text-gray-800 dark:text-gray-300 break-all">
                                    {{ log.request_id || '-' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Execution Context
                        </h2>
                        <div class="bg-gray-900 rounded-md p-4 overflow-x-auto">
                            <pre class="text-xs text-green-400 font-mono leading-relaxed"><code>{{ JSON.stringify(log.context, null, 2) }}</code></pre>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mt-6 mb-4">
                            Subjects
                        </h2>
                        <div class="bg-gray-900 rounded-md p-4 overflow-x-auto">
                            <pre class="text-xs text-blue-400 font-mono leading-relaxed"><code>{{ JSON.stringify(log.subjects, null, 2) }}</code></pre>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                            Rule Traces ({{ log.traces?.length || 0 }})
                        </h2>
                        <div class="space-y-4">
                            <div v-for="(trace, index) in log.traces" :key="trace.id" class="border dark:border-gray-700 rounded-lg overflow-hidden">
                                <div class="px-4 py-3 bg-gray-50 dark:bg-gray-900/50 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center justify-center h-6 w-6 rounded-full bg-gray-200 dark:bg-gray-700 text-xs font-medium text-gray-600 dark:text-gray-300">
                                            {{ index + 1 }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            Rule #{{ trace.logic_rule_id }}
                                            <span v-if="trace.rule" class="ml-1 text-gray-500 dark:text-gray-400 font-normal">
                                                ({{ trace.rule.name }})
                                            </span>
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ trace.duration }}ms
                                        </span>
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize border"
                                            :class="{
                                                'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-400 dark:border-green-800': trace.status === 'passed',
                                                'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-400 dark:border-red-800': trace.status === 'failed',
                                                'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700': trace.status === 'void'
                                                }"
                                            >{{ trace.status }}</span>
                                    </div>
                                </div>
                                <div class="p-4 bg-white dark:bg-gray-800 border-t dark:border-gray-700">
                                    <details class="group">
                                        <summary class="text-sm font-medium text-indigo-600 dark:text-indigo-400 cursor-pointer list-none flex items-center hover:text-indigo-800 dark:hover:text-indigo-300">
                                            <svg class="mr-2 h-4 w-4 transform transition-transform group-open:rotate-90"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                            </svg>
                                            View Snapshot
                                        </summary>
                                        <div class="mt-4 bg-gray-900 rounded-md p-4 overflow-x-auto">
                                            <pre class="text-xs text-gray-300 font-mono leading-relaxed"><code>{{ JSON.stringify(trace.snapshot, null, 2) }}</code></pre>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useApi } from '@/composables/useApi';

const api = useApi();
const route = useRoute();
const router = useRouter();

const log = ref(null);
const isLoading = ref(false);

const fetchLogDetails = async () => {
    isLoading.value = true;
    const apiResp = await api.get(`telemetry/${route.params.id}`);
    log.value = apiResp.data;
    isLoading.value = false;
};

onMounted(() => {
    fetchLogDetails();
});
</script>
