<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    data: Array,
    divisions: Array,
    filters: Object,
});

const startDate = ref(props.filters.start_date);
const endDate = ref(props.filters.end_date);
const type = ref(props.filters.type);
const divisionId = ref(props.filters.division_id || '');

const refreshReport = () => {
    router.get(route('reports.index'), { 
        start_date: startDate.value,
        end_date: endDate.value,
        type: type.value,
        division_id: divisionId.value
    }, { preserveState: true, replace: true });
};

const printReport = () => {
    window.print();
};

const exportExcel = () => {
    const params = new URLSearchParams({
        start_date: startDate.value,
        end_date: endDate.value,
        type: type.value,
        division_id: divisionId.value || ''
    });
    window.location.href = route('reports.export') + '?' + params.toString();
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    }).format(date);
};
</script>

<template>
    <Head title="Laporan Arsip Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Laporan Arsip Surat
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    
                    <!-- Filters (No Print) -->
                    <div class="print:hidden mb-6 flex flex-col xl:flex-row gap-4 items-end">
                        <div class="flex flex-col md:flex-row gap-4 flex-grow w-full">
                            <div class="w-full md:w-1/4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Laporan</label>
                                <select v-model="type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="incoming">Surat Masuk</option>
                                    <option value="outgoing">Surat Keluar</option>
                                </select>
                            </div>
                            
                            <div v-if="divisions && divisions.length > 0" class="w-full md:w-1/4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                                <select v-model="divisionId" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua Divisi</option>
                                    <option v-for="div in divisions" :key="div.id" :value="div.id">
                                        {{ div.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="flex gap-2 w-full md:w-1/2">
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                    <input v-model="startDate" type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div class="w-1/2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                    <input v-model="endDate" type="date" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 w-full xl:w-auto flex-shrink-0">
                            <button @click="refreshReport" class="flex items-center justify-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" title="Tampilkan">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="hidden md:inline">Tampilkan</span>
                            </button>
                            <button @click="exportExcel" class="flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" title="Export Excel">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span class="hidden md:inline">Excel</span>
                            </button>
                            <button @click="printReport" class="flex items-center justify-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" title="Print PDF">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 md:mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                                <span class="hidden md:inline">PDF</span>
                            </button>
                        </div>
                    </div>

                    <!-- Report Header (Print Only) -->
                    <div class="hidden print:block mb-8 text-center">
                        <h1 class="text-2xl font-bold">LAPORAN ARSIP SURAT {{ type === 'incoming' ? 'MASUK' : 'KELUAR' }}</h1>
                        <p>Periode: {{ formatDate(startDate) }} s/d {{ formatDate(endDate) }}</p>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">No</th>
                                    
                                    <!-- Kolom Khusus Surat Masuk -->
                                    <th v-if="type === 'incoming'" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">No. Agenda</th>
                                    
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Tanggal Surat</th>
                                    
                                    <!-- Kolom Khusus Surat Masuk -->
                                    <th v-if="type === 'incoming'" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Tgl Diterima</th>

                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Nomor Surat</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">
                                        {{ type === 'incoming' ? 'Pengirim' : 'Tujuan' }}
                                    </th>
                                    
                                    <!-- Kolom Khusus Surat Keluar -->
                                    <th v-if="type === 'outgoing'" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Divisi</th>

                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Perihal</th>

                                    <!-- Kolom Khusus Surat Masuk -->
                                    <th v-if="type === 'incoming'" class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="(item, index) in data" :key="item.id">
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">{{ index + 1 }}</td>
                                    
                                    <!-- Data Khusus Surat Masuk -->
                                    <td v-if="type === 'incoming'" class="px-4 py-2 whitespace-nowrap text-sm font-bold text-gray-900 border">{{ item.agenda_number }}</td>
                                    
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">{{ formatDate(item.mail_date) }}</td>
                                    
                                    <!-- Data Khusus Surat Masuk -->
                                    <td v-if="type === 'incoming'" class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">{{ formatDate(item.received_date) }}</td>

                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">{{ item.mail_number }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">
                                        {{ type === 'incoming' ? item.origin : item.recipient }}
                                    </td>
                                    
                                    <!-- Data Khusus Surat Keluar -->
                                    <td v-if="type === 'outgoing'" class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">
                                        {{ item.division?.name || '-' }}
                                    </td>

                                    <td class="px-4 py-2 text-sm text-gray-900 border">{{ item.subject }}</td>
                                    
                                    <!-- Data Khusus Surat Masuk -->
                                    <td v-if="type === 'incoming'" class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 border">
                                        <span 
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                            :class="{
                                                'bg-green-100 text-green-800': item.status === 'disposition',
                                                'bg-gray-100 text-gray-800': item.status === 'new',
                                                'bg-yellow-100 text-yellow-800': item.status === 'process'
                                            }"
                                        >
                                            {{ item.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="data.length === 0">
                                    <td :colspan="type === 'incoming' ? 9 : 7" class="px-4 py-4 text-center text-gray-500">Tidak ada data surat pada periode ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    @page { size: landscape; }
    body * {
        visibility: hidden;
    }
    #app, #app * {
        visibility: visible;
    }
    .print\:hidden {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
}
</style>
