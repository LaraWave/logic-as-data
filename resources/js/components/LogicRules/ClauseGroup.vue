<template>
    <div class="border-l-4 border-indigo-200 dark:border-indigo-900/60 pl-4 py-2 my-2 transition-colors">
        <div class="flex items-center justify-between mb-4 bg-gray-50 dark:bg-gray-800/50 p-2 rounded-lg border border-gray-100 dark:border-gray-800">
            <div class="flex items-center space-x-3">
                <select v-model="group.combinator" class="px-3 py-1.5 rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm font-bold text-indigo-700 dark:text-indigo-400 focus:ring-2 focus:ring-indigo-500/50 outline-none uppercase cursor-pointer shadow-sm">
                    <option value="and">AND</option>
                    <option value="or">OR</option>
                </select>
                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Group</span>
            </div>

            <div class="flex items-center space-x-2">
                <button @click="addRule" class="text-xs font-semibold px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-md shadow-sm transition-colors">
                    + Rule
                </button>
                <button @click="addGroup" class="text-xs font-semibold px-3 py-1.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-md shadow-sm transition-colors">
                    + Group
                </button>
                <button v-if="!isRoot" @click="$emit('remove')" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 p-1.5 transition-colors" title="Delete Entire Group">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>

        <div class="space-y-4 pl-2">
            <div v-if="!group.clauses || group.clauses.length === 0" class="text-sm text-gray-400 dark:text-gray-600 italic py-2">
                No conditions in this group.
            </div>

            <template v-for="(item, index) in group.clauses" :key="index">
                <ClauseGroup
                    v-if="item.combinator"
                    v-model="group.clauses[index]"
                    :isRoot="false"
                    @remove="removeClause(index)"
                />

                <ClauseRow
                    v-else
                    v-model="group.clauses[index]"
                    @remove="removeClause(index)"
                />
            </template>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import ClauseRow from './ClauseRow.vue';

defineOptions({ name: 'ClauseGroup' });

const props = defineProps({
    modelValue: { type: Object, required: true },
    isRoot: { type: Boolean, default: false }
});

const emit = defineEmits(['update:modelValue', 'remove']);

const group = computed(() => props.modelValue);

const addRule = () => {
    if (!group.value.clauses) {
        group.value.clauses = [];
    }
    group.value.clauses.push({
        source: { alias: '', params: {} },
        operator: '',
        target: { alias: 'core.literal', params: { 'value_type': 'string', 'value': '' } }
    });
};

const addGroup = () => {
    if (! group.value.clauses) {
        group.value.clauses = [];
    }
    group.value.clauses.push({
        combinator: 'and',
        clauses: [{
            source: { alias: '', params: {} },
            operator: '',
            target: { alias: 'core.literal', params: { 'value_type': 'string', 'value': '' } }
        }]
    });
};

const removeClause = (index) => {
    group.value.clauses.splice(index, 1);
};
</script>
