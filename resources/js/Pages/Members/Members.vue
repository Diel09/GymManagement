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
                                <button v-if="new Date(member.end_date) <= new Date()" type="button" class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
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
            console.log(this.name);
        }
    },
    props: ['members', 'totalItems', 'currentRange'],
}
</script>
