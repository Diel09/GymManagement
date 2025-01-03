<template>
    <AuthenticatedLayout title="Edit Expense">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-stretch">
                <h2 class="text-xl font-semibold leading-tight md:basis-11/12">
                    Edit Expense
                </h2>
                <div class="md:basis-1/12 flex gap-2">
                    <Link :href="route('expenses.home')" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <span>Back to Expenses</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <form @submit.prevent="updateExpense">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Expense Name</label>
                        <input v-model="ex.name" type="text" required class="block w-full mt-1 px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-800 dark:text-white" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Price</label>
                        <input v-model="ex.price" type="number" required class="block w-full mt-1 px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-800 dark:text-white" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Date of Purchase/Spend</label>
                        <input v-model="ex.date" type="date" required class="block w-full mt-1 px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-800 dark:text-white" />
                    </div>
                    
                    <button type="submit" class="w-full text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Update Expense
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Link } from '@inertiajs/inertia-vue3'

export default {
    props: ['expense'],
    data() {
        return {
            ex: { 
                id: this.expense.id,
                name: this.expense.title,
                price: this.expense.price,
                date: this.expense.date_spend
            }
        }
    },
    components: {
        AuthenticatedLayout, Link
    },
    methods: {
        updateExpense() {
            axios.post('/update_expense', this.ex)
                .then(response => {
                    if(response.data.status == 'success') {
                        this.$inertia.visit(route('expenses.home'))
                    } else {
                        console.error('Failed to save expense');
                    }
                })
        }
    }
}
</script>
