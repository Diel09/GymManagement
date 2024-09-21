<template>
    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-stretch">
                <h2 class="text-xl font-semibold leading-tight basis-10/12">
                    Memberships
                </h2>
                <div class="md:basis-2/12 flex gap-2">
                    <Link :href="route('memberships.add')" class="text-center md:w-full text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <span>Add Memberships</span>
                    </Link>
                </div>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Duration
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="membership in memberships.data" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ membership.name }}
                            </th>
                            <td class="px-6 py-4">
                                {{ membership.duration }}
                            </td>
                            <td class="px-6 py-4">
                                {{ membership.fee }}
                            </td>
                            <td class="p-2">
                                <Link :href="route('memberships.edit', { id: membership.id })" class="mx-2 text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-4 focus:ring-purple-300 font-medium rounded-md text-sm px-5 py-2.5 text-center mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                                    <span>Edit</span>
                                </Link>
                                <button
                                    @click="deleteMembership(membership.id)"
                                    class="mx-2 text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium text-sm px-5 py-2.5 text-center me-2 mb-2 rounded-md dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                >
                                    <span>Delete</span>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Paginator :paginator="memberships" :total="totalItems" :currentRange="currentRange"></Paginator>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Paginator from '@/Components/Paginator.vue'
import { Link } from '@inertiajs/inertia-vue3';

export default {
    data() {
        return {

        }
    },
    components: {
        AuthenticatedLayout, Link, Paginator
    },
    methods: {
        async deleteMembership(id) {
            if (confirm('Are you sure you want to delete this membership?')) {
                try {
                    await axios.delete(`/memberships/${id}/delete`);
                    window.location.href = '/memberships';
                } catch (error) {
                    console.error('There was an error deleting the membership:', error);
                    alert('Failed to delete the membership.');
                }
                    
            }
        },
    },
    props: ['memberships', 'totalItems', 'currentRange'],
    mounted() {

    }
}
</script>
