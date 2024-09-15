<template>
    <div class="backdrop-blur overflow-y-auto overflow-x-hidden fixed inset-0 z-50 flex justify-center items-center max-w-full max-h-full">
        <div class="relative p-4 w-full max-w-md">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <button @click="this.$emit('close')" type="button" class="absolute top-3 end-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="p-4 md:p-5 text-center">
                    <div class="flex justify-center items-center mb-5">
                        <qrcode-vue id="qrcode" ref="qrcodeContainer" :value="value" :size="250" class="w-full h-full qrcode-canvas"></qrcode-vue>
                    </div>
                    <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">{{ name }}</h3>
                    <button @click="downloadQRCode" class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center">
                        Download QR Code
                    </button>
                    <button @click="this.$emit('close')" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import QrcodeVue from 'qrcode.vue'

export default {
    components: {
        QrcodeVue
    },
    props: ['value', 'name'],
    methods: {
        downloadQRCode() {
            const canvas = document.getElementById('qrcode');
            
            // Create a temporary canvas to add margins
            const margin = 20; // Set your desired margin here
            const tempCanvas = document.createElement('canvas');
            const ctx = tempCanvas.getContext('2d');
            
            tempCanvas.width = canvas.width + margin * 2;
            tempCanvas.height = canvas.height + margin * 2;

            ctx.fillStyle = 'white'; 
            ctx.fillRect(0, 0, tempCanvas.width, tempCanvas.height);
            ctx.drawImage(canvas, margin, margin);

            const link = document.createElement('a')
            link.download = `${this.name}.png`
            link.href = tempCanvas.toDataURL()
            link.click()
        }
    }
}
</script>
