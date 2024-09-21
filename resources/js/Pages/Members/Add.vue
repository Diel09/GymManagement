<template>
    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-stretch">
                <h2 class="text-xl font-semibold leading-tight">
                    Add Member
                </h2>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <h2 class="text-2xl font-bold mb-6 text-gray-800 dark:text-white">Member Information</h2>
            <form @submit.prevent="saveMember" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">First Name</label>
                        <input v-model="member.first_name" type="text" placeholder="Firs Name" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.first_name" class="text-red-500 text-sm">{{ errors.first_name }}</span>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Last Name</label>
                        <input v-model="member.last_name" type="text" placeholder="Last Name" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.last_name_name" class="text-red-500 text-sm">{{ errors.last_name }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Middle Name</label>
                        <input v-model="member.middle_name" type="text" placeholder="Middle Name" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.middle_name" class="text-red-500 text-sm">{{ errors.middle_name }}</span>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Gender</label>
                        <select v-model="member.gender" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                            <option value="3">Others</option>
                        </select>
                        <span v-if="errors.gender" class="text-red-500 text-sm">{{ errors.gender }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Birthdate</label>
                        <input v-model="member.birthdate" type="date" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.birthdate" class="text-red-500 text-sm">{{ errors.birthdate }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Membership</label>
                        <select v-model="member.membership" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                            <option v-for="membership in memberships" :key="membership.id" :value="membership.id">{{ membership.name }}</option>
                        </select>
                        <span v-if="errors.membership" class="text-red-500 text-sm">{{ errors.membership }}</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white">Contact</label>
                        <input v-model="member.contact" type="text" placeholder="Contact" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                        <span v-if="errors.member" class="text-red-500 text-sm">{{ errors.member }}</span>
                    </div>
                </div>
                
                <div class="flex justify-end gap-2">
                    <Link :href="route('members.home')" class="py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        <span>Cancel</span>
                    </Link>
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
import { Inertia } from '@inertiajs/inertia';

export default {
    data() {
        return {
            member: {
                first_name: '',
                last_name: '',
                middle_name: '',
                gender: '',
                birthdate: '',
                membership: '',
                contact: ''
            },
            errors: {}
        }
    },
    components: {
        AuthenticatedLayout, Link
    },
    methods: {
        saveMember() {
            this.errors = {}
            if (!this.member.first_name) {
                this.errors.first_name = "First name is required.";
            }

            if (!this.member.last_name) {
                this.errors.last_name = "Last name is required.";
            }

            if (!this.member.middle_name) {
                this.errors.middle_name = "Middle name is required.";
            }

            if (!this.member.gender) {
                this.errors.gender = "Gender is required.";
            }

            if (!this.member.birthdate) {
                this.errors.birthdate = "Birthdate is required.";
            }

            if (!this.member.membership) {
                this.errors.membership = "Membership is required.";
            }

            if (!this.member.contact) {
                this.errors.contact = "Contact is required.";
            }

            if(Object.keys(this.errors).length == 0) {
                axios.post('/save_member', this.member).then((response) => {
                    if(response.data.status == 'success') {
                        Inertia.visit('/members');
                    } else {
                        console.error('Failed to save member');
                    }
                });
            }
        }
    },
    props: ['memberships'],
}
</script>
