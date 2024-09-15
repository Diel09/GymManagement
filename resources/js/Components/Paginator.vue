<template>
    <nav class="flex items-center flex-col flex-wrap md:flex-row justify-between pt-4" aria-label="Table navigation">
        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 mb-4 md:mb-0 block w-full md:inline md:w-auto">
            Showing <span class="font-semibold text-gray-900 dark:text-white">{{ currentRange }}</span> of <span class="font-semibold text-gray-900 dark:text-white">{{ total }}</span>
        </span>
        <ul class="inline-flex -space-x-px rtl:space-x-reverse text-sm h-8">
        <li>
            <button
            @click="goToPage(paginator.current_page - 1)" 
            :disabled="!paginator.prev_page_url"
            class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
            >
                Previous
            </button>
        </li>
        <li v-for="page in totalPages" :key="page">
            <button
                @click="goToPage(page)"
                :class="{
                    'text-blue-600 bg-blue-50 border-blue-300 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-700 dark:text-white': page === paginator.current_page,
                    'text-gray-500 bg-white border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white': page !== paginator.current_page
                }"
                class="flex items-center justify-center px-3 h-8 leading-tight border"
                >
                {{ page }}
            </button>
        </li>
        <li>
            <button
                @click="goToPage(paginator.current_page + 1)"
                :disabled="!paginator.next_page_url"
                class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                >
                Next
            </button>
        </li>
        </ul>
    </nav>
</template>
  
<script>
    export default {
        props: {
            paginator: Object,
            total: Number,
            currentRange: String,
        },
        computed: {
            totalPages() {
                return Array.from({ length: this.paginator.last_page }, (_, i) => i + 1);
            },
        },
        methods: {
            goToPage(page) {
                if (page < 1 || page > this.paginator.last_page) return;

                this.$inertia.visit(`${this.paginator.path}?page=${page}`, {
                    preserveState: true,
                });
            },
        },
    };
</script>

  