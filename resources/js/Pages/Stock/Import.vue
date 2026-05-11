<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const activeTab = ref('box');

const boxForm = useForm({
    start_meter_number: '',
});

const normalForm = useForm({
    meter_numbers: '',
});

const largeForm = useForm({
    start_meter: '',
    end_meter: '',
});

const submitBox = () => {
    boxForm.post(route('stock.import.box'), {
        onSuccess: () => boxForm.reset(),
    });
};

const submitNormal = () => {
    normalForm.post(route('stock.import.normal'), {
        onSuccess: () => normalForm.reset(),
    });
};

const submitLarge = () => {
    largeForm.post(route('stock.import.large'), {
        onSuccess: () => largeForm.reset(),
    });
};
</script>

<template>
    <Head title="Stock Import" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Import</h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 sm:p-6 text-gray-900">
                        
                        <!-- Mobile Friendly Tabs -->
                        <div class="flex border-b border-gray-200 mb-6 overflow-x-auto">
                            <button 
                                @click="activeTab = 'box'"
                                :class="{'border-indigo-500 text-indigo-600': activeTab === 'box', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'box'}"
                                class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm flex-1 text-center"
                            >
                                Box Import
                            </button>
                            <button 
                                @click="activeTab = 'normal'"
                                :class="{'border-indigo-500 text-indigo-600': activeTab === 'normal', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'normal'}"
                                class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm flex-1 text-center"
                            >
                                Normal Import
                            </button>
                            <button 
                                @click="activeTab = 'large'"
                                :class="{'border-indigo-500 text-indigo-600': activeTab === 'large', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'large'}"
                                class="whitespace-nowrap py-4 px-4 border-b-2 font-medium text-sm flex-1 text-center"
                            >
                                Large Import
                            </button>
                        </div>

                        <!-- Box Import Form -->
                        <div v-if="activeTab === 'box'" class="space-y-4">
                            <div class="bg-blue-50 p-4 rounded-md mb-4">
                                <p class="text-sm text-blue-700">
                                    <strong>Auto Series Generator:</strong> Enter the first meter number. System will generate 5 sequential meters. Must end in 1 or 6.
                                </p>
                            </div>
                            <form @submit.prevent="submitBox">
                                <div>
                                    <label for="start_meter_number" class="block text-sm font-medium text-gray-700">Start Meter Number</label>
                                    <input 
                                        type="text" 
                                        id="start_meter_number" 
                                        v-model="boxForm.start_meter_number"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-3"
                                        placeholder="e.g. C1350156"
                                    >
                                    <p v-if="boxForm.errors.start_meter_number" class="text-red-500 text-xs mt-1">{{ boxForm.errors.start_meter_number }}</p>
                                </div>
                                <div class="mt-4">
                                    <button 
                                        type="submit" 
                                        :disabled="boxForm.processing"
                                        class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Generate Box Series
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Normal Import Form -->
                        <div v-if="activeTab === 'normal'" class="space-y-4">
                            <div class="bg-gray-50 p-4 rounded-md mb-4">
                                <p class="text-sm text-gray-700">
                                    <strong>Single/Multi Input:</strong> Enter comma-separated meter numbers.
                                </p>
                            </div>
                            <form @submit.prevent="submitNormal">
                                <div>
                                    <label for="meter_numbers" class="block text-sm font-medium text-gray-700">Meter Numbers</label>
                                    <textarea 
                                        id="meter_numbers" 
                                        v-model="normalForm.meter_numbers"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        placeholder="C1350140, C1350143, C170195"
                                    ></textarea>
                                    <p v-if="normalForm.errors.meter_numbers" class="text-red-500 text-xs mt-1">{{ normalForm.errors.meter_numbers }}</p>
                                </div>
                                <div class="mt-4">
                                    <button 
                                        type="submit" 
                                        :disabled="normalForm.processing"
                                        class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Import Meters
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Large Import Form -->
                        <div v-if="activeTab === 'large'" class="space-y-4">
                            <div class="bg-green-50 p-4 rounded-md mb-4">
                                <p class="text-sm text-green-700">
                                    <strong>Range Mode:</strong> Import bulk meters. System will auto-group them into boxes of 5.
                                </p>
                            </div>
                            <form @submit.prevent="submitLarge" class="space-y-4">
                                <div>
                                    <label for="start_meter" class="block text-sm font-medium text-gray-700">Start Meter</label>
                                    <input 
                                        type="text" 
                                        id="start_meter" 
                                        v-model="largeForm.start_meter"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-3"
                                        placeholder="C1350150"
                                    >
                                    <p v-if="largeForm.errors.start_meter" class="text-red-500 text-xs mt-1">{{ largeForm.errors.start_meter }}</p>
                                </div>
                                <div>
                                    <label for="end_meter" class="block text-sm font-medium text-gray-700">End Meter</label>
                                    <input 
                                        type="text" 
                                        id="end_meter" 
                                        v-model="largeForm.end_meter"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-lg p-3"
                                        placeholder="C1350240"
                                    >
                                    <p v-if="largeForm.errors.end_meter" class="text-red-500 text-xs mt-1">{{ largeForm.errors.end_meter }}</p>
                                </div>
                                <div class="mt-4">
                                    <button 
                                        type="submit" 
                                        :disabled="largeForm.processing"
                                        class="w-full inline-flex justify-center py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Import Range
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Success Message -->
                        <div v-if="$page.props.flash.success" class="mt-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ $page.props.flash.success }}</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
