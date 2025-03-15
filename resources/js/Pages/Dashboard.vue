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

            <!-- Dropdown to Select Report Type -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200">Select Report Type:</label>
                <select v-model="selectedReportType" @change="updateChart" class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none dark:bg-gray-800 dark:text-white">
                    <option value="daily">Daily Report</option>
                    <option value="weekly">Weekly Report</option>
                    <option value="monthly">Monthly Report</option>
                </select>
            </div>

            <!-- Chart Display -->
            <div class="mt-4" :style="{ height: '60vh' }">
                <Line :data="chartData" :options="chartOptions" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import { Chart as ChartJS, Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale } from 'chart.js'
import { Line } from 'vue-chartjs'

ChartJS.register(Title, Tooltip, Legend, LineElement, PointElement, CategoryScale, LinearScale)

export default {
    data() {
        return {
            selectedReportType: 'monthly', // Default selection
            chartData: this.getChartData('monthly'),
            chartOptions: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        ticks: {
                            stepSize: 100, 
                        }
                    }
                }
            }
        }
    },
    props: {
        new_mem: Number,
        walk_in: Number,
        member_in: Number,
        dailySales: {
            type: Object,
            default: () => ({ labels: [], data: [] })
        },
        weeklySales: {
            type: Object,
            default: () => ({ labels: [], data: [] })
        },
        monthlySales: {
            type: Object,
            default: () => ({ labels: [], data: [] })
        }
    },
    components: {
        AuthenticatedLayout, Line
    },
    methods: {
        // Function to return chart data based on selection
        getChartData(type) {
            let labels = [];
            let dataset = [];

            if (type === 'daily' && this.dailySales) {
                // Convert day numbers into an array of labels
                labels = Object.keys(this.dailySales).map(day => `Day ${day}`);
                // Convert sales values into an array of data
                dataset = Object.values(this.dailySales);
            } else if (type === 'weekly' && this.weeklySales) {
                // Convert week numbers into an array of labels
                labels = Object.keys(this.weeklySales).map(week => `Week ${week}`);
                // Convert sales values into an array of data
                dataset = Object.values(this.weeklySales);
            } else if (type === 'monthly' && this.monthlySales) {
                // Convert object keys (month names) into an array of labels
                labels = Object.keys(this.monthlySales);
                // Convert object values (sales data) into an array of values
                dataset = Object.values(this.monthlySales);
            }

            return {
                labels: labels,
                datasets: [
                    {
                        label: `${type.charAt(0).toUpperCase() + type.slice(1)} Sales`,
                        backgroundColor: 'rgba(37, 99, 235, 0.5)',
                        borderColor: '#2563eb',
                        fill: true,
                        data: dataset,
                    }
                ]
            };
        },
        // Update chart data when selection changes
        updateChart() {
            this.chartData = this.getChartData(this.selectedReportType);
        }
    },
    mounted() {
        console.log('Daily Sales:', this.dailySales);
        console.log('Weekly Sales:', this.weeklySales);
        console.log('Monthly Sales:', this.monthlySales);

        console.log('Chart Data:', this.getChartData(this.selectedReportType));
    }
}
</script>
