<template>
    <AuthenticatedLayout title="Reports">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-stretch">
                <h2 class="text-xl font-semibold leading-tight basis-10/12">
                    Reports
                </h2>
            </div>
        </template>

        <div class="flex justify-center p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <div class="h-full grid grid-rows-12">
                <div class="flex items-center">
                    <div class="relative">
                        <VueDatePicker v-model="date.start" :enable-time-picker="false"></VueDatePicker>
                        <!-- <input v-model="date.start" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date start"> -->
                    </div>
                    <span class="mx-4 text-gray-500">to</span>
                    <div class="relative">
                        <VueDatePicker v-model="date.end" :enable-time-picker="false"></VueDatePicker>
                        <!-- <input v-model="date.end" type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full ps-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Select date end"> -->
                    </div>
                    <button
                        @click="download()"
                        class="mx-2 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center me-2 rounded-md dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-900"
                    >
                        <span>Download</span>
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import VueDatePicker from '@vuepic/vue-datepicker';
import { Link } from '@inertiajs/inertia-vue3';

export default {
    data() {
        return {
            date: {
                start: null,
                end: null
            }
        }
    },
    components: {
        AuthenticatedLayout, Link, VueDatePicker
    },
    methods: {
        download() {
            axios.post('/reports/download', {
                start: this.date.start.toLocaleDateString(),
                end: this.date.end.toLocaleDateString(),
            },{
                responseType: "blob",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/pdf"
                }
            }).then(response => {
                const fileName = this.date.start.toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'}) + ' to ' + this.date.end.toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'}) + ' Report';
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', fileName + '.pdf');
                document.body.appendChild(link);
                link.click()

            }).catch(error => {
                console.error(error);
            })
        }
    },
    mounted() {

    }
}
</script>
