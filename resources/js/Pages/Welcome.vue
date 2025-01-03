<template>
    <Head title="Welcome" />

    <div class="relative flex items-top justify-center min-h-screen bg-gray-900 dark:bg-dark-bg dark:text-gray-200 sm:items-center sm:pt-0">
        <div v-if="canLogin" class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
            <Link v-if="$page.props.auth.user" href="/dashboard" class="text-sm text-white underline">
                Dashboard
            </Link>

            <template v-else>
                <Link :href="route('login')" class="text-sm text-white underline">
                    Log in
                </Link>
            </template>
        </div>

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-md w-full max-w-7xl px-4">
                <div class="grid grid-cols-1 md:grid-cols-2 h-auto w-auto">
                    <div class="px-4 py-2">
                        <div class="text-center p-5">
                            <h1 class="text-xl font-bold">HeatDrops Attendance System</h1>
                        </div>
                        <div class="bg-gray-200 rounded-lg p-3 mb-3 text-center">
                            <p class="text-lg font-semibold">REMINDER:</p>
                            <p>Please tap your RFID only. Honesty is the best policy.</p>
                        </div>
                        
                        <div v-if="msg" class="mt-3 bg-gray-200 rounded-lg p-3 mb-3 text-center">
                            <p class="text-lg font-semibold">{{ msg }}</p>
                        </div>

                        <!-- RFID Form -->
                        <form @submit.prevent="onDecodeForm">
                            <input
                                v-model="rfidInput"
                                id="rfid-input"
                                type="text"
                                autocomplete="off"
                                autofocus
                                class="absolute opacity-0 w-0 h-0"
                                @blur="focusInput"
                            />
                        </form>

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
                                    <li>If you are not a member, approach someone from the management.</li>
                                    <li>Tap your RFID.</li>
                                </ul>
                                <p class="mt-2">Press F5 to refresh the system if it does not work.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 p-5 text-center">
                            <button 
                                @click="toggleIn"
                                :class="{'bg-blue-600 text-white': activeIn, 'bg-gray-200 text-md font-semibold': !activeIn}"
                                class="p-4 rounded">
                                Time IN
                            </button>

                            <button 
                                @click="toggleOut"
                                :class="{'bg-blue-600 text-white': activeOut, 'bg-gray-200 text-md font-semibold': !activeOut}"
                                class="p-4 rounded">
                                Time OUT
                            </button>
                        </div>
                        <div class="text-center py-6">
                            <p class="font-bold text-lg text-green-600">Tap your RFID in the Scanner</p>
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
    components: { Head, Link },
    data() {
        return {
            day: '',
            date: '',
            time: '',
            rfidInput: '',
            msg: '',
            activeIn: false,
            activeOut: false,
            lastScanned: 0,
        }
    },
    methods: {
        updateDateTime() {
            const now = new Date();
            this.day = now.toLocaleDateString('en-US', { weekday: 'long' });
            this.date = now.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
            this.time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: 'numeric', second: 'numeric', hour12: true });
        },
        onDecodeForm() {
            const currentTime = Date.now();

            if (currentTime - this.lastScanned < 3000) {
                return;
            }
            this.lastScanned = currentTime;

            if (this.rfidInput.trim() === '') {
                this.msg = 'Please scan your RFID';

                setTimeout(() => {
                    this.msg = '';  // Clear the message after 3 seconds
                }, 3000);
                return;
            }

            if (this.activeIn || this.activeOut) {
                axios.post('/time', {
                    id: this.rfidInput,
                    active: this.activeIn
                }).then((response) => {
                    this.msg = response.data.msg;
                    this.rfidInput = ''; // Clear the input field

                    setTimeout(() => {
                        this.msg = '';  // Clear the message after 3 seconds
                    }, 3000);
                });
            } else {
                this.msg = 'Please select one [Time in / Time out].';
                setTimeout(() => {
                    this.msg = '';  // Clear the message after 3 seconds
                }, 3000);
            }
            this.rfidInput = ''; // Clear the input field
        },
        toggleIn() {
            this.activeIn = true;
            this.activeOut = false;
        },
        toggleOut() {
            this.activeIn = false;
            this.activeOut = true;
        },
        focusInput() {
            this.$nextTick(() => {
                document.getElementById('rfid-input').focus();
            });
        },
    },
    mounted() {
        setInterval(() => {
            this.updateDateTime();
        }, 1000);
        this.focusInput(); // Ensure the input is focused on mount
        window.addEventListener('click', this.focusInput); // Reapply focus on any click
    },
    beforeUnmount() {
        window.removeEventListener('click', this.focusInput); // Cleanup event listener
    },
    props: ['canLogin', 'canRegister']
}
</script>
