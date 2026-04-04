<template>
    <div class="flex items-start space-x-3 p-3 bg-white dark:bg-gray-950 border border-gray-200 dark:border-gray-800 rounded-lg shadow-sm transition-colors relative group">
        <div class="flex-1 space-y-2">
            <div class="flex items-center justify-between">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Source</label>
            </div>

            <div class="space-y-2 p-2 bg-gray-50 dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800">
                <select
                    v-model="clause.source.alias"
                    @change="handleOperandChange('source')"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50 outline-none">
                    <option value="" disabled>Select</option>
                    <option v-for="(source, alias) in extractors" :key="alias" :value="alias">
                        {{ source.metadata.label }}
                    </option>
                </select>
                <div v-if="Object.keys(currentExtractor?.metadata?.fields || {}).length" class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <MetadataFields 
                        :fields="currentExtractor.metadata.fields" 
                        v-model="clause.source.params" 
                    />
                </div>
            </div>
        </div>

        <div class="w-48 space-y-2">
            <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Operator</label>
            <div class="space-y-2 p-2 bg-gray-50 dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800">
            <select v-model="clause.operator" class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50 outline-none">
                <option value="" disabled>Select</option>
                <option v-for="(operator, alias) in operators" :key="alias" :value="alias">
                    {{ operator.metadata.label }}
                </option>
            </select>
        </div>
        </div>

        <div class="flex-1 space-y-2">
            <div class="flex items-center justify-between">
                <label class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Target</label>
            </div>

            <div class="space-y-2 p-2 bg-gray-50 dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-800">
                <select
                    v-model="clause.target.alias"
                    @change="handleOperandChange('target')"
                    class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500/50 outline-none">
                    <option value="" disabled>Select</option>
                    <option v-for="(target, alias) in resolvers" :key="alias" :value="alias">
                        {{ target.metadata.label }}
                    </option>
                </select>
                <div v-if="Object.keys(currentResolver?.metadata?.fields || {}).length" class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <MetadataFields
                        :fields="currentResolver.metadata.fields" 
                        v-model="clause.target.params" 
                    />
                </div>
            </div>
        </div>

        <div class="pt-7">
            <button @click="$emit('remove')" class="text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition-colors p-2 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20" title="Remove Condition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useConfig } from '@/composables/useConfig';
import MetadataFields from './MetadataFields.vue';

const props = defineProps({
    modelValue: { type: Object, required: true }
});

const emit = defineEmits(['update:modelValue', 'remove']);

const clause = computed(() => props.modelValue);

const { extractors, operators, resolvers, actions } = useConfig();

const currentExtractor = computed(() => {
    if (! clause.value.source.alias) return null;
    return extractors[clause.value.source.alias] || null;
});

const currentResolver = computed(() => {
    if (! clause.value.target.alias) return null;
    return resolvers[clause.value.target.alias] || null;
});

const handleOperandChange = (operandType) => {
    const alias = clause.value[operandType].alias;
    let metadata = null;
    if (operandType === 'source') {
        metadata = extractors[alias].metadata || null;
    } else if (operandType === 'target') {
        metadata = resolvers[alias].metadata || null;
    }

    clause.value[operandType].params = {};

    if (metadata?.fields) {
        for (const [key, fieldDef] of Object.entries(metadata.fields)) {
            if (fieldDef.default !== undefined) {
                clause.value[operandType].params[key] = fieldDef.default;
            }
        }
    }
};
</script>
