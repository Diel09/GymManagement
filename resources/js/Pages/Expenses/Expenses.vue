<template>
    <AuthenticatedLayout title="Expenses">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-stretch">
                <h2 class="text-xl font-semibold leading-tight md:basis-11/12">
                    Expenses
                </h2>
                <div class="md:basis-1/12 flex gap-2">
                    <Link :href="route('expenses.add')" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <span>Add Expense</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table v-if="expenses.data && expenses.data.length > 0" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Title</th>
                            <th scope="col" class="px-6 py-3">Price</th>
                            <th scope="col" class="px-6 py-3">Date of Purchase/Spend</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="expense in expenses.data" :key="expense.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ expense.title }}</td>
                            <td class="px-6 py-4">{{ expense.price | currency }}</td>
                            <td class="px-6 py-4">{{ formatDate(expense.date_spend) }}</td>
                            <td class="px-6 py-4">
                                <td class="p-2">
                                <Link :href="route('expenses.edit', { id: expense.id })" class="mx-2 text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-4 focus:ring-purple-300 font-medium rounded-md text-sm px-5 py-2.5 text-center mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                                    <span>Edit</span>
                                </Link>
                                <!-- <button
                                    @click="deleteMembership(expense.id)"
                                    class="mx-2 text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium text-sm px-5 py-2.5 text-center me-2 mb-2 rounded-md dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                >
                                    <span>Delete</span>
                                </button> -->
                            </td>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="p-5">
                    No expenses Found!
                </div>
            </div>
            <Paginator :paginator="expenses" :total="totalItems" :currentRange="currentRange"></Paginator>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Link } from '@inertiajs/inertia-vue3'
import Paginator from '@/Components/Paginator.vue'

export default {
    props: ['expenses', 'totalItems', 'currentRange'],
    components: {
        AuthenticatedLayout, Link, Paginator
    },
    methods: {
        deleteExpense(id) {
            if (confirm('Are you sure you want to delete this expense?')) {
                axios.delete(`/expenses/${id}`).then(response => {
                    location.reload();
                }).catch(error => {
                    console.error(error);
                    alert('Error deleting expense');
                });
            }
        }, 
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                weekday: 'long', // "Monday"
                year: 'numeric', // "2025"
                month: 'long', // "October"
                day: 'numeric', // "12"
            });
        }
    }
}
</script>
