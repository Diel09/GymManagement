<template>
    <Head title="Welcome" />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-900 dark:bg-dark-bg dark:text-gray-200 sm:items-center sm:pt-0">
        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link v-if="$page.props.auth.user" href="/dashboard" class="text-sm text-gray-700 underline">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')" class="text-sm text-white underline">
                    Log in
                </Link>

                <!-- <Link v-if="canRegister" :href="route('register')" class="ml-4 text-sm text-gray-700 underline">
                    Register
                </Link> -->
            </template>
        </div>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md w-full max-w-7xl px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 h-auto w-auto">
                    <div class="px-4 py-2">
                        <div class="text-center p-5">
                            <h1 class="text-xl font-bold">Gym Attendance Recording System</h1>
                        </div>
                        <div class="bg-gray-200 rounded-lg p-3 mb-3 text-center">
                            <p class="text-lg font-semibold">REMINDER:</p>
                            <p>Please scan your own QR code only. Honesty is the best policy.</p>
                        </div>
                    </div>
                    <div class="px-4 py-2">
                        <div class="flex justify-center items-center">
                            <div class="bg-blue-600 text-white text-3xl font-bold p-4 rounded-lg w-full max-w-s text-center">
                                <div id="date" class="mb-2">{{ day }}, {{ date }}</div>
                                <div id="time" class="text-5xl">{{ time }}</div>
                            </div>
                        </div>

                        <div class="text-center p-5">
                            <hr class="border-t-2 border-gray-300 my-4">

                            <div class="text-left text-sm mb-4">
                                <p class="font-semibold">How to use:</p>
                                <ul class="list-decimal pl-5">
                                    <li>Make sure that the correct time period is selected.</li>
                                    <li>Click <strong>Guest IN</strong> or <strong>Guest OUT</strong> below if you are a GUEST.</li>
                                    <li>Scan your QR code</li>
                                </ul>
                                <p class="mt-2">Press F5 to refresh the system if it does not work and make sure that the computer has an internet connection.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 p-5 text-center">
                            <div class="p-4 bg-gray-200 text-md font-semibold rounded">Time IN</div>
                            <div class="p-4 bg-gray-200 text-md font-semibold rounded">Time OUT</div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 p-5 text-center">
                            <div class="p-4 bg-gray-200 text-md font-semibold rounded">Guest IN</div>
                            <div class="p-4 bg-gray-200 text-md font-semibold rounded">Guest OUT</div>
                        </div>
                        <div class="text-center py-6">
                            <p class="font-bold text-lg text-green-600">SCAN YOUR QR Code</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import { Head, Link } from '@inertiajs/inertia-vue3';
export default {
    components: {Head, Link},
    data() {
        return {
            day: '',
            date: '',
            time: ''
        }
    },
    methods: {
        updateDateTime() {
            const now = new Date();

            this.day = now.toLocaleDateString('en-US', {weekday: 'long'});
            this.date = now.toLocaleDateString('en-US', {month: 'long', day: 'numeric', year: 'numeric'});
            this.time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: 'numeric', second: 'numeric', hour12: true })
        }
    },
    mounted() {
        setInterval(() => {
            this.updateDateTime()
        }, 1000);
    },
    props: ['canLogin', 'canRegiser']
}
</script>
