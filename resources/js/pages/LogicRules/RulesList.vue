<template>
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm dark:shadow-black/20 border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-200">
        
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                {{ isTrashMode ? 'Deleted Rules (Trash)' : 'Configured Rules' }}
            </h2>

            <div class="flex items-center space-x-3">
                <router-link 
                    v-if="!isTrashMode"
                    :to="{ name: 'logic-rules.create' }"
                    class="flex items-center space-x-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950"
                    title="Create new rule"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Create Rule</span>
                </router-link>

                <button
                    @click="toggleTrash" 
                    :class="isTrashMode ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400'"
                    class="flex items-center space-x-2 px-4 py-2 rounded-lg text-sm font-semibold transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path v-if="!isTrashMode" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l5 5m-5-5l5-5" />
                    </svg>
                    <span>{{ isTrashMode ? 'Back to Active' : 'View Trash' }}</span>
                </button>
            </div>
        </div>

        <div v-if="isLoading" class="flex items-center space-x-3 text-gray-500 dark:text-gray-400 py-8 justify-center">
            <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="font-medium tracking-tight">Loading logic rules...</span>
        </div>

        <div v-else-if="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800/50 rounded-lg p-4 text-red-600 dark:text-red-400 text-sm font-medium">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.217a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                <span>{{ error }}</span>
            </div>
        </div>

        <div v-else-if="logicRules.length === 0" class="text-center py-12 border-2 border-dashed border-gray-100 dark:border-gray-700/50 rounded-xl">
            <p class="text-gray-500 dark:text-gray-400 font-medium italic">
                No logic rules found. Create one to get started.
            </p>
        </div>

        <div v-else class="space-y-6">
            <div v-for="(hookRules, hookName) in logicRules" :key="hookName" class="bg-gray-50 dark:bg-gray-800/40 rounded-xl p-4 border border-gray-200 dark:border-gray-700 transition-colors">
                <button @click="toggleHook(hookName)" class="w-full flex items-center justify-between pb-2 group cursor-pointer focus:outline-none">
                    <div class="flex items-center space-x-3">
                        <span class="px-2.5 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300 rounded-md text-xs font-bold uppercase tracking-wider transition-colors group-hover:bg-indigo-200 dark:group-hover:bg-indigo-800/60">
                            Hook
                        </span>
                        <h3 class="text-sm font-bold text-gray-800 dark:text-gray-100">
                            {{ hookName }}
                        </h3>

                        <span class="text-gray-500 dark:text-gray-400 text-xs font-medium bg-gray-200 dark:bg-gray-700 px-2 py-0.5 rounded-full">
                            {{ hookRules.length }} rules
                        </span>
                    </div>

                    <svg :class="{'rotate-180': collapsedHooks[hookName]}" 
                    class="w-5 h-5 text-gray-400 dark:text-gray-500 transition-transform duration-200" 
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div v-show="!collapsedHooks[hookName]" class="space-y-3 pt-3 mt-1 border-t border-gray-200 dark:border-gray-700/60">
                    <div v-for="rule in hookRules" :key="rule.id" 
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-sm hover:shadow-md dark:hover:shadow-gray-900/50 transition-all duration-200 flex items-center justify-between group/card">
                        <div class="flex items-center space-x-4">
                            <div class="flex flex-col items-center justify-center w-12 h-12 bg-gray-50 dark:bg-gray-900 rounded border border-gray-100 dark:border-gray-700">
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 uppercase font-bold tracking-widest">Pri</span>
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-200">{{ rule.priority }}</span>
                            </div>

                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">{{ rule.name }}</h4>
                                <div class="flex items-center space-x-2 mt-1">
                                    <span :class="statusColor(rule.status)" class="w-2 h-2 rounded-full"></span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 capitalize font-medium">{{ rule.status }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <template v-if="!isTrashMode">
                                <router-link
                                    :to="{ name: 'logic-rules.edit', params: { id: rule.id } }"
                                    class="flex items-center space-x-1 px-3 py-1.5 text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded transition-colors" title="Edit rule">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <span>Edit</span>
                                </router-link>

                                <button @click="deleteRule(rule.id)" class="flex items-center space-x-1 px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors cursor-pointer" title="Delete rule">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    <span>Delete</span>
                                </button>
                            </template>

                            <template v-else>
                                <button @click="restoreRule(rule.id)" 
                                    class="flex items-center space-x-1 px-3 py-1.5 text-sm font-medium text-green-600 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/30 rounded transition-colors cursor-pointer" title="Restore rule">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <span>Restore</span>
                                </button>

                                <button @click="forceDeleteRule(rule.id)" class="flex items-center space-x-1 px-3 py-1.5 text-sm font-medium text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors cursor-pointer" title="Permanently delete this rule">
                                    Permanent Delete
                                </button>
                            </template>
                        </div>
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
const logicRules = ref([]);
const isLoading = ref(false);
const error = ref(null);
const collapsedHooks = ref({});
const isTrashMode = ref(false);

const fetchRules = async () => {
    isLoading.value = true;
    error.value = null;
    try {
        const params = isTrashMode.value ? { trashed: 'only' } : {};
        let apiResp = await api.get('logic-rules', params);
        logicRules.value = apiResp.data;
    } catch (err) {
        error.value = err || 'Failed to load logic rules.';
        console.log(err);
    } finally {
        isLoading.value = false;
    }
};

const statusColor = (status) => {
    const colors = {
        active: 'bg-green-500 dark:bg-green-400 shadow-[0_0_8px_rgba(34,197,94,0.4)]',
        draft: 'bg-gray-400 dark:bg-gray-500',
        testing: 'bg-purple-500 dark:bg-purple-400',
        archived: 'bg-red-400 dark:bg-red-500'
    };
    return colors[status] || 'bg-gray-300';
};

const toggleHook = (hookName) => {
    collapsedHooks.value[hookName] = !collapsedHooks.value[hookName];
};

const toggleTrash = () => {
    isTrashMode.value = !isTrashMode.value;
    fetchRules();
};

const restoreRule = async (id) => {
    try {
        await api.patch(`logic-rules/${id}/restore`);
        fetchRules();
    } catch (err) {
        console.error('Failed to restore:', err);
    }
};

const deleteRule = async (id) => {
    const message = 'Are you sure you want to move this rule to the trash?';

    if (! confirm(message)) return;

    try {
        const endpoint = `logic-rules/${id}`;
        await api.del(endpoint);
        await fetchRules();
        console.log('Rule trashed.');
    } catch (err) {
        alert('Failed to delete the rule: ' + err.message);
    }
};

const forceDeleteRule = async (id) => {
    const message = 'Are you sure? This will permanently delete the rule and cannot be undone.';

    if (! confirm(message)) return;

    try {
        const endpoint = `logic-rules/${id}/force`;
        await api.patch(endpoint);
        await fetchRules();
        console.log('Rule purged.');
    } catch (err) {
        alert('Failed to permanently delete the rule: ' + err.message);
    }
};

onMounted(() => {
    fetchRules();
});
</script>
