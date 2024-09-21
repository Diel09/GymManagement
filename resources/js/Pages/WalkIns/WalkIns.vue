<template>
    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Walk-ins
                </h2>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <div v-if="msg" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-gray-800 dark:text-green-400" role="alert">
                <span class="font-medium">{{ this.msg }}</span>
            </div>
            <div v-if="error" class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-800 dark:text-red-400" role="alert">
                <span class="font-medium">{{ this.error }}</span>
            </div>
            <form @submit.prevent="saveWalkIn" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">First Name</label>
                        <input v-model="walk_in.first_name" type="text" placeholder="Firs Name" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.first_name" class="text-red-500 text-sm">{{ errors.first_name }}</span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Last Name</label>
                        <input v-model="walk_in.last_name" type="text" placeholder="Last Name" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.last_name_name" class="text-red-500 text-sm">{{ errors.last_name }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Middle Name</label>
                        <input v-model="walk_in.middle_name" type="text" placeholder="Middle Name" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.middle_name" class="text-red-500 text-sm">{{ errors.middle_name }}</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Amount</label>
                        <input v-model="walk_in.amount" type="numeric" placeholder="Amount" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.amount" class="text-red-500 text-sm">{{ errors.amount }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Contact</label>
                        <input v-model="walk_in.contact" type="numeric" placeholder="Contact" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.contact" class="text-red-500 text-sm">{{ errors.contact }}</span>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Link } from '@inertiajs/inertia-vue3';

export default {
    data() {
        return {
            walk_in: {
                first_name: '',
                last_name: '',
                middle_name: '',
                amount: '',
                contact: ''
            },
            errors: {},
            msg: '',
            error: ''
        }
    },
    components: {
        AuthenticatedLayout, Link
    },
    methods: {
        saveWalkIn() {
            this.errors = {}
            if (!this.walk_in.first_name) {
                this.errors.first_name = "First name is required.";
            }

            if (!this.walk_in.last_name) {
                this.errors.last_name = "Last name is required.";
            }

            if (!this.walk_in.middle_name) {
                this.errors.middle_name = "Middle name is required.";
            }

            if (!this.walk_in.amount) {
                this.errors.amount = "Amount is required.";
            }

            if (!this.walk_in.contact) {
                this.errors.contact = "Contact is required.";
            }

            if(Object.keys(this.errors).length == 0) {
                axios.post('/save_walkin', this.walk_in).then((response) => {
                    if(response.data.status == 'success') {
                        Object.keys(this.walk_in).forEach(key => this.walk_in[key] = '');
                        this.msg = response.data.msg;

                        setTimeout(() => {
                            this.msg = '';  // Clear the message after 3 seconds
                        }, 3000);
                    } else {
                        this.error = response.data.msg;

                        setTimeout(() => {
                            this.error = '';  // Clear the message after 3 seconds
                        }, 3000);
                    }
                });
            }
        }
    },
    props: ['memberships'],
}
</script>
