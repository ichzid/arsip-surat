<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
});

const classes = computed(() =>
    props.active
        ? 'group flex items-center px-3 py-2.5 text-sm font-medium rounded-md bg-indigo-50 text-indigo-600'
        : 'group flex items-center px-3 py-2.5 text-sm font-medium rounded-md text-gray-700 hover:text-indigo-600 hover:bg-gray-50'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot name="icon" />
        <span class="ml-3 flex-1"><slot /></span>
    </Link>
</template>
