<template>
    <AuthenticatedLayout title="Dashboard">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Dashboard
                </h2>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <div class="grid md:grid-rows-1 md:grid-flow-col gap-4">
                <a href="#" class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ new_mem }}</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">New Members</p>
                </a>
                <a href="#" class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ walk_in }}</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Walk-Ins Today</p>
                </a>
                <a href="#" class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ member_in }}</h5>
                    <p class="font-normal text-gray-700 dark:text-gray-400">Members Time-in</p>
                </a>
            </div>
            <div class="mt-4" :style="{ height: '63vh' }">
                <Bar :data="data" :options="options" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'
import { Bar } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)
export default {
    data() {
        return {
            data: {
                datasets: [
                    {
                        label: 'Monthly Sales',
                        backgroundColor: '#9333ea',
                        data: this.sales,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        ticks: {
                        stepSize: 100, // Set the interval between ticks to 100
                        }
                    }
                }
            },
        }
    },
    props: ['new_mem', 'walk_in', 'member_in', 'sales'],
    components: {
        AuthenticatedLayout, Bar
    },
    methods: {
        
    },
    mounted() {
        console.log('Sales prop:', this.sales);
    }
}
</script>


