<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    letter: Object,
    divisions: Array,
    auth: Object,
});

const dispositionForm = useForm({
    to_division_id: '',
    notes: '',
    instruction: '',
    due_date: '',
});

const submitDisposition = () => {
    dispositionForm.post(route('dispositions.store', props.letter.id), {
        onSuccess: () => dispositionForm.reset(),
    });
};

const updateStatus = (dispositionId, status) => {
    if (confirm('Update status tindak lanjut?')) {
        router.patch(route('dispositions.update-status', dispositionId), {
            status: status
        });
    }
};

// Check permissions
const userRoles = computed(() => props.auth.user.roles.map(r => r.name));
const canDispose = computed(() => {
    return userRoles.value.includes('Admin') || 
           userRoles.value.includes('Operator Divisi Umum') || 
           userRoles.value.includes('Pimpinan');
});

const canUpdateStatus = (disposition) => {
    // Admin or the target division user
    return userRoles.value.includes('Admin') || 
           props.auth.user.division_id === disposition.to_division_id;
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
    <Head title="Detail Surat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detail Surat: {{ letter.agenda_number }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <a 
                            v-if="canDispose && letter.dispositions.length > 0"
                            :href="route('incoming-letters.print-disposition', letter.id)"
                            target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Lembar Disposisi
                        </a>
                    </div>
                    <Link
                        :href="route('incoming-letters.index')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </Link>
                </div>

                <!-- Letter Details -->
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-lg font-bold mb-4">Informasi Surat</h3>
                                <p><strong>Nomor Surat:</strong> {{ letter.mail_number }}</p>
                                <p><strong>Tanggal Surat:</strong> {{ formatDate(letter.mail_date) }}</p>
                                <p><strong>Diterima Tanggal:</strong> {{ formatDate(letter.received_date) }}</p>
                                <p><strong>Asal:</strong> {{ letter.origin }}</p>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold mb-4">File & Status</h3>
                                <p class="mb-2"><strong>Status:</strong> 
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ letter.status.toUpperCase() }}
                                    </span>
                                </p>
                                <div v-if="letter.file_path" class="mt-4">
                                    <a :href="'/storage/' + letter.file_path" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">
                                        Lihat File Lampiran
                                    </a>
                                </div>
                                <div v-else class="text-gray-500 italic">Tidak ada lampiran file.</div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <strong>Perihal:</strong>
                            <p class="mt-1 p-4 bg-gray-50 rounded-md">{{ letter.subject }}</p>
                        </div>
                    </div>
                </div>

                <!-- Disposition Form -->
                <div v-if="canDispose" class="bg-white shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 text-gray-900">Buat Disposisi Baru</h3>
                        <form @submit.prevent="submitDisposition" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tujuan Divisi</label>
                                <select v-model="dispositionForm.to_division_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="" disabled>Pilih Divisi</option>
                                    <option v-for="div in divisions" :key="div.id" :value="div.id">
                                        {{ div.name }}
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Batas Waktu (Opsional)</label>
                                <input v-model="dispositionForm.due_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Instruksi / Informasi</label>
                                <select v-model="dispositionForm.instruction" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="" disabled>Pilih Instruksi</option>
                                    <option value="Untuk ditindak lanjuti">Untuk ditindak lanjuti</option>
                                    <option value="Untuk penyelesaian">Untuk penyelesaian</option>
                                    <option value="Untuk diketahui">Untuk diketahui</option>
                                    <option value="Untuk perhatian">Untuk perhatian</option>
                                    <option value="Untuk saran/pendapat">Untuk saran/pendapat</option>
                                    <option value="Harap dibicarakan dengan saya">Harap dibicarakan dengan saya</option>
                                    <option value="Arsip">Arsip</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Catatan Tambahan</label>
                                <textarea v-model="dispositionForm.notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                            <div class="md:col-span-2 text-right">
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700" :disabled="dispositionForm.processing">
                                    Kirim Disposisi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Disposition History -->
                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4 text-gray-900">Riwayat Disposisi</h3>
                        
                        <div v-if="letter.dispositions.length === 0" class="text-gray-500 italic">Belum ada disposisi.</div>
                        
                        <div v-else class="flow-root">
                            <ul role="list" class="-mb-8">
                                <li v-for="(disp, dispIdx) in letter.dispositions" :key="disp.id">
                                    <div class="relative pb-8">
                                        <span v-if="dispIdx !== letter.dispositions.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center ring-8 ring-white">
                                                    <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        Disposisi dari <span class="font-medium text-gray-900">{{ disp.from_division?.name }}</span> ke <span class="font-medium text-gray-900">{{ disp.to_division?.name }}</span>
                                                    </p>
                                                    <p v-if="disp.instruction" class="mt-1 text-sm font-medium text-indigo-600">Instruksi: {{ disp.instruction }}</p>
                                                    <p class="mt-1 text-sm text-gray-600 italic">"{{ disp.notes || 'Tidak ada catatan tambahan' }}"</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <time :datetime="disp.created_at">{{ new Date(disp.created_at).toLocaleDateString() }}</time>
                                                    <div class="mt-1">
                                                        <span 
                                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                                            :class="{
                                                                'bg-green-100 text-green-800': disp.status === 'done',
                                                                'bg-yellow-100 text-yellow-800': disp.status === 'process',
                                                                'bg-gray-100 text-gray-800': disp.status === 'pending'
                                                            }"
                                                        >
                                                            {{ disp.status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Action Buttons for Target Division -->
                                        <div v-if="canUpdateStatus(disp) && disp.status !== 'done'" class="ml-12 mt-2">
                                            <div class="flex space-x-2">
                                                <Link 
                                                    :href="route('dispositions.update-status', disp.id)" 
                                                    method="patch" 
                                                    :data="{ status: 'process' }"
                                                    as="button"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors"
                                                    v-if="disp.status === 'pending'"
                                                >
                                                    Proses
                                                </Link>
                                                <Link 
                                                    :href="route('dispositions.update-status', disp.id)" 
                                                    method="patch" 
                                                    :data="{ status: 'done' }"
                                                    as="button"
                                                    class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium bg-green-50 text-green-700 hover:bg-green-100 transition-colors"
                                                >
                                                    Selesai
                                                </Link>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
