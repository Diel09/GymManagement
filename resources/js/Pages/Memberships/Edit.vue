<template>
    <AuthenticatedLayout title="Add Membership">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-stretch">
                <h2 class="text-xl font-semibold leading-tight">
                    Add Memberships
                </h2>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <form @submit.prevent="updateMembership" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-white">Membership Name</label>
                        <input type="text" v-model="membership.name" placeholder="Membership name" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.name" class="text-red-500 text-sm">{{ errors.name }}</span>
                    </div>
                    
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-white">Fee</label>
                        <input type="number" v-model="membership.fee" placeholder="Fee (PHP)" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.fee" class="text-red-500 text-sm">{{ errors.fee }}</span>
                    </div>

                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 dark:text-white">Duration</label>
                        <input type="number" v-model="membership.duration" placeholder="Duration in months" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.duration" class="text-red-500 text-sm">{{ errors.duration }}</span>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="flex justify-end gap-2">
                    <Link :href="route('memberships.home')" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        <span>Cancel</span>
                    </Link>
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Link } from '@inertiajs/inertia-vue3';
import { Inertia } from '@inertiajs/inertia';

export default {
    data() {
        return {
            membership: {
                name: this.membership.name,
                fee: this.membership.fee,
                duration: this.membership.duration
            },
            errors: {}
        }
    },
    components: {
        AuthenticatedLayout, Link
    },
    props: ['membership'],
    methods: {
        updateMembership() {
            this.errors = {}
            if (!this.membership.name) {
                this.errors.name = "Membership name is required.";
            }

            if (!this.membership.fee) {
                this.errors.fee = "Fee is required.";
            } else if (isNaN(this.membership.fee)) {
                this.errors.fee = "Fee must be a number.";
            }

            if (!this.membership.duration) {
                this.errors.duration = "Duration is required.";
            } else if (this.membership.duration <= 0) {
                this.errors.duration = "Duration must be a positive number.";
            } else if (this.membership.duration > 12) {
                this.errors.duration = "Duration must be a less than 12";
            }

            if(Object.keys(this.errors).length == 0) {
                axios.post('/update_membership', this.membership).then((response) => {
                    if(response.data.status == 'success') {
                        Inertia.visit('/memberships');
                    } else {
                        console.error('Failed to save membership');
                    }
                });
            }
        }
    }
}
</script>
