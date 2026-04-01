<template>
    <div class="max-w-6xl mx-auto pb-12">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center space-x-4">
                <button @click="router.push({ name: 'logic-rules.index' })" class="text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200 transition-colors cursor-pointer">
                    ← Back to Rules Listing
                </button>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                    {{ isEditing ? 'Edit Rule' : 'Create New Rule' }}
                </h1>
            </div>

            <button
                @click="saveRule" 
                :disabled="isSaving"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center space-x-2 shadow-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950">
                <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isSaving ? 'Saving...' : 'Save Rule' }}</span>
            </button>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-800 pb-2">Rule Configuration</h2>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                
                <div class="md:col-span-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Rule Name</label>
                    <input 
                        v-model="logicRule.name" 
                        type="text" 
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 dark:focus:ring-indigo-500/50 transition-all duration-200"
                        placeholder="e.g., Black Friday VIP Discount"
                    >
                </div>

                <div class="md:col-span-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Execution Hook</label>
                    <input 
                        v-model="logicRule.hook" 
                        type="text" 
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 shadow-sm font-mono text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 dark:focus:ring-indigo-500/50 transition-all duration-200"
                        placeholder="e.g., cart.checkout"
                    >
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">The event/hook in your app that triggers this rule.</p>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                    <select 
                        v-model="logicRule.status" 
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-gray-900 dark:text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 dark:focus:ring-indigo-500/50 transition-all duration-200 appearance-none"
                    >
                        <option value="draft">Draft</option>
                        <option value="testing">Testing</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>

                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Priority</label>
                    <input 
                        v-model.number="logicRule.priority" 
                        type="number" 
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 dark:focus:ring-indigo-500/50 transition-all duration-200"
                        placeholder="0"
                    >
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">Higher numbers run first.</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 mb-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Conditions (Predicate)</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">WHEN should this rule apply?</p>
                </div>
            </div>

            <div class="bg-gray-50/50 dark:bg-gray-950/50 border border-gray-200 dark:border-gray-800 rounded-xl p-4">
				<ClauseGroup v-model="logicRule.definition.predicate" :isRoot="true" />
			</div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 p-6 mb-8">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Actions</h2>
                <p class="text-gray-500 text-sm">Define what happens when the conditions are met.</p>
            </div>

            <ActionList v-model="logicRule.definition.actions" />
        </div>

        <div class="flex justify-end">
            <button
                @click="saveRule" 
                :disabled="isSaving"
                class="bg-emerald-500 hover:bg-emerald-600 text-white px-12 py-3 rounded-lg font-medium transition-colors disabled:opacity-50 flex items-center space-x-2 shadow-sm cursor-pointer mr-4 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950">
                <svg v-if="isSaving" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>{{ isSaving ? 'Saving...' : 'Save Rule' }}</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useApi } from '@/composables/useApi';
import { flash } from '@/services/flash';
import ClauseGroup from '@/components/LogicRules/ClauseGroup.vue';
import ActionList from '@/components/LogicRules/ActionList.vue';

const api = useApi();
const route = useRoute();
const router = useRouter();

const isEditing = computed(() => !!route.params.id);
const isSaving = ref(false);

const logicRule = ref({
    name: '',
    hook: '',
    definition: {
        predicate: { combinator: 'and', clauses: [] },
        actions: []
    },
    priority: 0,
    status: 'draft',
});

const fetchRule = async () => {
    const apiResp = await api.get(`logic-rules/${route.params.id}`);
    logicRule.value = apiResp.data;
};

const saveRule = async () => {
    isSaving.value = true;

    try {
        const endpoint = isEditing.value
            ? `logic-rules/${route.params.id}` 
            : `logic-rules`;

        const method = isEditing.value ? 'put' : 'post';

        const apiResp = await api[method](endpoint, logicRule.value);

        flash.show(apiResp.message, 'success');

        router.push({ name: 'logic-rules.index' });
    } catch (error) {
        console.error("Failed to save rule", error);
        flash.show(error.response?.data?.message || 'Error saving rule', 'danger');
    } finally {
        isSaving.value = false;
    }
};

onMounted(() => {
    if (isEditing.value) {
        fetchRule();
    }
});
</script>
