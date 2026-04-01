<template>
    <div class="space-y-4" v-if="hasFields">
        <div v-for="(field, key) in fields" :key="key" class="space-y-1.5">
            <label
                :for="inputId = getId(key)" 
                class="block text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ field.label || key }}
                <span v-if="field.required" class="text-red-500 dark:text-red-400 ml-0.5" title="Required">*</span>
            </label>

            <input
                v-if="!(fieldType = resolveInputType(field)) || ['string', 'text'].includes(fieldType)"
                v-bind="resolveDynamicAttributes(field)"
                :id="inputId"
                v-model="params[key]" 
                type="text" 
                :placeholder="field.placeholder || ''"
                :required="field.required"
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/40 outline-none transition-all shadow-sm"
            >

            <input 
                v-else-if="fieldType === 'number'"
                v-bind="resolveDynamicAttributes(field)"
                :id="inputId"
                v-model.number="params[key]"
                type="number" 
                :placeholder="field.placeholder || '0'"
                :required="field.required"
                class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/40 outline-none transition-all shadow-sm"
            >

            <div v-else-if="fieldType === 'select'" class="relative">
                <select
                    :id="inputId"
                    v-bind="resolveDynamicAttributes(field)"
                    v-model="params[key]"
                    :required="field.required"
                    class="w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-950 text-sm text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/40 outline-none transition-all shadow-sm appearance-none cursor-pointer"
                >
                    <option v-if="!field.default && !params[key]" value="" disabled>Select</option>
                    <option
                        v-for="option in field.options"
                        :key="option.value"
                        :value="option.value"
                    >
                        {{ option.label }}
                    </option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>

            <div v-if="['boolean', 'toggle'].includes(fieldType)" class="flex items-center py-2">
                <button
                    v-bind="resolveDynamicAttributes(field)"
                    type="button"
                    role="switch"
                    :aria-checked="!!params[key]"
                    @click="params[key] = !params[key]"
                    :class="[
                        params[key] ? 'bg-indigo-600' : 'bg-gray-200 dark:bg-gray-700',
                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-950'
                    ]"
                >
                    <span class="sr-only">Toggle {{ field.label }}</span>
                    
                    <span
                        aria-hidden="true"
                        :class="[
                            params[key] ? 'translate-x-5' : 'translate-x-0',
                            'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                        ]"
                    />
                </button>

                <span
                    class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-100 cursor-pointer select-none"
                    @click="params[key] = !params[key]"
                >
                    {{ params[key] ? 'Yes' : 'No' }}
                </span>
            </div>

            <p v-if="field.help" class="text-[10px] text-gray-500 dark:text-gray-400 mt-1 leading-relaxed">
                {{ field.help }}
            </p>

        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    fields: { 
        type: Object, 
        default: () => ({}) 
    },
    modelValue: { 
        type: Object, 
        default: () => ({}) 
    },
    idPrefix: {
        type: String,
        default: () => Math.random().toString(36).substring(2, 9)
    }
});

const emit = defineEmits(['update:modelValue']);

const hasFields = computed(() => {
    return props.fields && Object.keys(props.fields).length > 0;
});

const params = computed({
    get: () => props.modelValue || {},
    set: (val) => emit('update:modelValue', val)
});

const getId = (key) => `${props.idPrefix}-${key}`;

const resolveInputType = (field) => {
    if (field.type !== 'dynamic') {
        return field.type;
    }

    const dependentValue = params.value[field.depends_on];

    return field.type_map?.[dependentValue] || field.default_type || 'text';
};

const resolveDynamicAttributes = (field) => {
    if (field.type !== 'dynamic' || !field.attributes_map || !field.depends_on) {
        return {};
    }

    const dependentValue = params.value[field.depends_on];

    return field.attributes_map[dependentValue] || {};
};
</script>
