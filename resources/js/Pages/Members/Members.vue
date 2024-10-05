<template>
    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-stretch">
                <h2 class="text-xl font-semibold leading-tight md:basis-11/12">
                    Members
                </h2>
                <div class="md:basis-1/12 flex gap-2">
                    <Link :href="route('members.add')" class="text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 font-medium rounded-lg text-sm px-4 py-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        <span>Add Members</span>
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
                                Membership
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Membership Due Date
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="member in members.data" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ member.first_name }} {{ member.middle_name[0] }}. {{ member.last_name }}
                            </th>
                            <td class="px-6 py-4">
                                {{ member.name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ member.end_date }}
                            </td>
                            <td class="px-6 py-4">
                                <span v-if="new Date(member.end_date) >= new Date()" class="text-green-500 font-semibold">
                                    Active
                                </span>
                                <span v-else class="text-red-500 font-semibold">
                                    Expired
                                </span>
                            </td>
                            <td class="p-2">
                                <Link :href="route('members.edit', { id: member.id })" class="mx-2 text-white bg-purple-700 hover:bg-purple-800 focus:outline-none focus:ring-4 focus:ring-purple-300 font-medium rounded-md text-sm px-5 py-2.5 text-center mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">
                                    <span>Edit</span>
                                </Link>
                                <!-- <button
                                    @click="deleteMembership(member.id)"
                                    class="mx-2 text-white bg-red-700 hover:bg-red-800 focus:outline-none focus:ring-4 focus:ring-red-300 font-medium text-sm px-5 py-2.5 text-center me-2 mb-2 rounded-md dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"
                                >
                                    <span>Delete</span>
                                </button> -->
                                <button @click="renew(member.id)" v-if="new Date(member.end_date) <= new Date()" type="button" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                                    Renew
                                </button>

                                <button @click="generateQR(member.id)" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                                    Generate QR
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <Paginator :paginator="members" :total="totalItems" :currentRange="currentRange"></Paginator>
            <QRcode v-if="showQr" :name="name" :value="text" @close="showQr = false"></QRcode>

            <!-- Main modal -->
            <div v-if="showModal" class="backdrop-blur overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex justify-center items-center max-w-full max-h-full">
                <div class="relative p-4 w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                Renew {{ this.name }} ?
                            </h3>
                            <button @click="showModal=false" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="static-modal">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>
                        <!-- Modal body -->
                        <div class="p-4 md:p-5 space-y-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-white">Membership</label>
                                <select v-model="membership" class="dark:text-black mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500 sm:text-sm">
                                    <option v-for="membership in memberships" :key="membership.id" :value="membership.id">{{ membership.name }}</option>
                                </select>
                            </div>
                        <!-- Modal footer -->
                        <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button @click="renewal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Link } from '@inertiajs/inertia-vue3';
import Paginator from '@/Components/Paginator.vue'
import QRcode from '@/Components/QRCode.vue'

export default {
    data() {
        return {
            showQr: false,
            text: '',
            showModal: false,
            membership: '',
            memberId: ''
        }
    },
    components: {
        AuthenticatedLayout, Link, Paginator, QRcode
    },
    methods: {
        generateQR(id) {
            let m = this.members.data;
            let member = m.find(obj => obj.id === id);

            this.showQr = true;
            this.text = String(id);
            this.name = member.first_name + ' ' + member.last_name;
        },
        renew(id) {
            let m = this.members.data;
            let member = m.find(obj => obj.id === id);

            this.memberId = id;

            this.showModal = true;
            this.name = member.first_name + ' ' + member.last_name;
        },
        renewal() {
            axios.post('/renew', {
                'id': this.memberId,
                'membership': this.membership
            }).then(response => {
                if(response.data.status == 'success') {
                    this.showModal = false;
                    this.memberId = '';
                    location.reload();
                }
                
            })
        }
    },
    props: ['members', 'totalItems', 'currentRange', 'memberships'],
}
</script>
