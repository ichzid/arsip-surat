<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    letter: Object,
});

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
    <Head title="Detail Surat Keluar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Detail Surat Keluar
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="flex justify-end mb-6">
                    <Link
                        :href="route('outgoing-letters.index')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali
                    </Link>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg border border-gray-100 mb-6">
                    <div class="p-6 text-gray-900">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-lg font-bold mb-4">Informasi Surat</h3>
                                <p><strong>Nomor Surat:</strong> {{ letter.mail_number }}</p>
                                <p><strong>Tanggal Surat:</strong> {{ formatDate(letter.mail_date) }}</p>
                                <p><strong>Tujuan:</strong> {{ letter.recipient }}</p>
                                <p><strong>Divisi Pembuat:</strong> {{ letter.division?.name }}</p>
                                <p><strong>Dibuat Oleh:</strong> {{ letter.creator?.name }}</p>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold mb-4">File Lampiran</h3>
                                <div v-if="letter.file_path" class="mt-4">
                                    <a :href="'/storage/' + letter.file_path" target="_blank" class="text-indigo-600 hover:text-indigo-900 underline">
                                        Lihat File Surat
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

            </div>
        </div>
    </AuthenticatedLayout>
</template>
