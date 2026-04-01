<template>
    <div class="space-y-4">
        <!-- <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5"> -->
        <div class="flex flex-col gap-5 items-center">
            <ActionRow
                v-for="(action, index) in localActions"
                :key="index"
                v-model="localActions[index]"
                @remove="removeAction(index)"
            />
        </div>

        <div v-if="localActions.length === 0" class="p-6 text-center border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-xl">
            <p class="text-sm text-gray-500 dark:text-gray-400">No actions defined. The logic will pass, but nothing will happen.</p>
        </div>

        <div class="flex justify-center">
            <button
                @click="addAction"
                type="button"
                class="flex items-center justify-center w-full sm:w-auto cursor-pointer gap-2 px-5 py-1.5 text-sm font-medium text-sky-50 bg-sky-500 hover:bg-sky-600 dark:text-sky-100 dark:bg-sky-700 dark:hover:bg-sky-600 border border-transparent rounded transition-colors"
            >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                </svg>
                Add Action
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import ActionRow from './ActionRow.vue';

const props = defineProps({
    modelValue: { type: Array, required: true, default: () => [] }
});

const emit = defineEmits(['update:modelValue']);

const localActions = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
});

const addAction = () => {
    localActions.value.push({ alias: '', params: {} });
};

const removeAction = (index) => {
    localActions.value.splice(index, 1);
};
</script>
