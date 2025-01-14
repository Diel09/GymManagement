<template>
    <AuthenticatedLayout title="Reports">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-stretch">
                <h2 class="text-xl font-semibold leading-tight basis-10/12">
                    Reports
                </h2>
            </div>
        </template>

        <div class="p-6 bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <!-- Container for Date Picker and Button -->
            <div class="flex items-center mb-4">
                <div class="relative mr-4">
                    <VueDatePicker 
                        v-model="date" 
                        :enable-time-picker="false" 
                        class="border-gray-300 rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 dark:focus:ring-blue-500 dark:focus:border-blue-500"
                    ></VueDatePicker>
                </div>
                <button
                    @click="updateList"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 rounded-md dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900"
                >
                    Submit
                </button>
            </div>

            <!-- Table Section -->
            <div class="relative overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table v-if="list.length > 0" class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Name</th>
                                <th scope="col" class="px-6 py-3">Contact</th>
                                <th scope="col" class="px-6 py-3">Time in</th>
                                <th scope="col" class="px-6 py-3">Time out</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="l in list" :key="l.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ l.first_name }} {{ l.middle_name[0] }}. {{ l.last_name }}
                                </th>
                                <td class="px-6 py-4">{{ l.contact }}</td>
                                <td class="px-6 py-4">{{ l.in }}</td>
                                <td class="px-6 py-4">{{ l.out }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else class="p-5">
                        No Time-in for this day
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import VueDatePicker from '@vuepic/vue-datepicker';
import axios from 'axios';

export default {
    data() {
        return {
            date: new Date(),
            list: [],
        };
    },
    components: {
        AuthenticatedLayout,
        VueDatePicker,
    },
    mounted() {
        this.updateList();
    },
    methods: {
        async updateList() {
            if (!this.date) {
                alert('Please select a date.');
                return;
            }

            try {
                const response = await axios.post('/fetch_member_in', { date: this.date });
                this.list = response.data; // Update the list with the new data
            } catch (error) {
                console.error('Error updating list:', error);
                alert('Failed to update the list. Please try again.');
            }
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        },
        formatTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleTimeString('en-US');
        },
    },
};
</script>
