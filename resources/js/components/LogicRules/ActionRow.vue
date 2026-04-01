<template>
    <div class="w-full lg:w-2/3 flex flex-col gap-3 bg-gray-50/50 dark:bg-gray-950/50 border border-gray-200 dark:border-gray-800 rounded-xl p-4 shadow-sm">
        <div class="flex items-center justify-between gap-4">
            <div class="flex-1">
                <label class="block mb-1 text-xs font-semibold text-gray-500 uppercase tracking-wider">Action</label>
                <select
                    v-model="localAction.alias"
                    @change="handleActionChange"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none appearance-none cursor-pointer"
                >
                    <option value="" disabled>Select</option>
                    <option v-for="(action, alias) in actions" :key="alias" :value="alias">
                        {{ action.metadata.label }}
                    </option>
                </select>
            </div>

            <button
                @click="$emit('remove')"
                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                title="Remove Action"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
            </button>
        </div>

        <div
            v-if="Object.keys(currentAction?.metadata?.fields || {}).length"
            class="pt-3 mt-1 border-t border-gray-100 dark:border-gray-700/50"
        >
            <h4 class="mb-3 text-xs font-medium text-gray-500 dark:text-gray-400">Action Parameters</h4>
            <MetadataFields
                :fields="currentAction.metadata.fields" 
                v-model="localAction.params" 
            />
        </div>
    </div>
</template>

<script setup>
import { computed, watch } from 'vue';
import { useConfig } from '@/composables/useConfig';
import MetadataFields from './MetadataFields.vue';

const props = defineProps({
    modelValue: { type: Object, required: true }
});

const emit = defineEmits(['update:modelValue', 'remove']);

const localAction = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
});

const { actions } = useConfig();

const currentAction = computed(() => {
    if (! localAction.value.alias) return null;
    return actions[localAction.value.alias] || null;
});

const handleActionChange = () => {
    localAction.value.params = {};

    if (Object.keys(currentAction?.value?.metadata?.fields || {}).length) {
        Object.entries(currentAction?.value.metadata.fields).forEach(([key, field]) => {
            if (field.default !== undefined) {
                localAction.value.params[key] = field.default;
            }
        });
    }
};
</script>
