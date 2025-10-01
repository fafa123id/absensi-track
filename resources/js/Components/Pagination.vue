<script setup>
import { computed } from 'vue';
import SecondaryButton from './SecondaryButton.vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    totalPages: {
        type: Number,
        required: true,
    },
    maxVisibleButtons: {
        type: Number,
        default: 5, 
    },
});

const emit = defineEmits(['page-changed']);

const pages = computed(() => {
    const range = [];
    const start = Math.max(1, props.currentPage - Math.floor(props.maxVisibleButtons / 2));
    const end = Math.min(props.totalPages, start + props.maxVisibleButtons - 1);

    const finalStart = Math.max(1, end - props.maxVisibleButtons + 1);

    for (let i = finalStart; i <= end; i++) {
        range.push({
            number: i,
            isActive: i === props.currentPage,
        });
    }
    return range;
});

const changePage = (page) => {
    if (page > 0 && page <= props.totalPages) {
        emit('page-changed', page);
    }
};
</script>

<template>
    <div v-if="totalPages > 1" class="flex justify-center items-center space-x-2">
        <SecondaryButton @click="changePage(1)" :disabled="currentPage === 1">
            &laquo;
        </SecondaryButton>
        <SecondaryButton @click="changePage(currentPage - 1)" :disabled="currentPage === 1">
            <
        </SecondaryButton>

        <button
            v-for="page in pages"
            :key="page.number"
            @click="changePage(page.number)"
            class="px-4 py-2 text-sm border rounded-md"
            :class="{
                'bg-blue-600 text-white border-blue-600': page.isActive,
                'hover:bg-gray-100': !page.isActive,
                'cursor-not-allowed': page.isDisabled,
            }"
            :disabled="page.isDisabled"
        >
            {{ page.number }}
        </button>

        <SecondaryButton @click="changePage(currentPage + 1)" :disabled="currentPage === totalPages">
           >
        </SecondaryButton>
        <SecondaryButton @click="changePage(totalPages)" :disabled="currentPage === totalPages">
            &raquo;
        </SecondaryButton>
    </div>
</template>