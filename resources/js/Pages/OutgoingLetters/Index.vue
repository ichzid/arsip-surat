<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    letters: Object,
    filters: Object,
    can: Object,
    current_user_id: Number,
});

const search = ref(props.filters.search || '');
const startDate = ref(props.filters.start_date || '');
const endDate = ref(props.filters.end_date || '');

// Debounce helper
const debounce = (fn, delay) => {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), delay);
    };
};

const updateFilters = debounce(() => {
    router.get(route('outgoing-letters.index'), { 
        search: search.value,
        start_date: startDate.value,
        end_date: endDate.value
    }, { preserveState: true, replace: true });
}, 300);

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    }).format(date);
};

const deleteLetter = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus surat ini?')) {
        router.delete(route('outgoing-letters.destroy', id));
    }
};

const canManage = (letter) => {
    return props.can.manage_all || letter.created_by === props.current_user_id;
};

watch([search, startDate, endDate], updateFilters);
</script>

<template>
    <Head title="Surat Keluar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Surat Keluar
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 text-gray-900">
                        
                        <div class="mb-6 flex justify-between items-center">
                             <h3 class="text-lg font-medium text-gray-900">Daftar Surat Keluar</h3>
                             <Link
                                v-if="can.create"
                                :href="route('outgoing-letters.create')"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500"
                            >
                                + Catat Surat Keluar
                            </Link>
                        </div>

                        <!-- Search & Filters -->
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                                <input 
                                    v-model="search" 
                                    type="text" 
                                    placeholder="Cari No Surat / Tujuan / Perihal..." 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                                <input 
                                    v-model="startDate" 
                                    type="date" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Dari Tanggal"
                                >
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                                <input 
                                    v-model="endDate" 
                                    type="date" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Sampai Tanggal"
                                >
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Surat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Surat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tujuan</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Divisi Pembuat</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="letter in letters.data" :key="letter.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">
                                            {{ letter.mail_number }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">{{ letter.subject }}</div>
                                            <div class="text-sm text-gray-500">{{ formatDate(letter.mail_date) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ letter.recipient }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ letter.division?.name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <Link :href="route('outgoing-letters.show', letter.id)" class="p-2 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition-colors" title="Detail">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </Link>
                                            <a v-if="letter.file_path" :href="'/storage/' + letter.file_path" target="_blank" class="p-2 bg-green-50 text-green-600 rounded hover:bg-green-100 transition-colors" title="Lihat File">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </a>
                                            <Link v-if="canManage(letter)" :href="route('outgoing-letters.edit', letter.id)" class="p-2 bg-yellow-50 text-yellow-600 rounded hover:bg-yellow-100 transition-colors" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </Link>
                                            <button v-if="canManage(letter)" @click="deleteLetter(letter.id)" class="p-2 bg-red-50 text-red-600 rounded hover:bg-red-100 transition-colors" title="Hapus">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex justify-end">
                            <template v-for="link in letters.links" :key="link.label">
                                <Link 
                                    v-if="link.url" 
                                    :href="link.url" 
                                    class="px-3 py-1 border rounded mr-1"
                                    :class="link.active ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700'"
                                    v-html="link.label"
                                />
                                <span v-else class="px-3 py-1 border rounded mr-1 text-gray-400" v-html="link.label"></span>
                            </template>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
