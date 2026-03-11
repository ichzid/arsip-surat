<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    letter: Object,
});

const form = useForm({
    _method: 'PUT',
    mail_number: props.letter.mail_number,
    mail_date: props.letter.mail_date ? props.letter.mail_date.split('T')[0] : '',
    recipient: props.letter.recipient,
    subject: props.letter.subject,
    file: null,
});

const submit = () => {
    form.post(route('outgoing-letters.update', props.letter.id));
};
</script>

<template>
    <Head title="Edit Surat Keluar" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Surat Keluar
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg border border-gray-100">
                    <form @submit.prevent="submit">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- No Surat -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Surat</label>
                                <input v-model="form.mail_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <div v-if="form.errors.mail_number" class="text-red-500 text-sm mt-1">{{ form.errors.mail_number }}</div>
                            </div>

                            <!-- Tujuan Surat -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tujuan Surat</label>
                                <input v-model="form.recipient" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- Tanggal Surat -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Surat</label>
                                <input v-model="form.mail_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            </div>

                            <!-- File Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">File Surat (Opsional, PDF Max 5MB)</label>
                                <div v-if="letter.file_path" class="text-xs text-gray-500 mb-2">
                                    File saat ini: <a :href="'/storage/' + letter.file_path" target="_blank" class="text-indigo-600 hover:underline">Lihat File</a>
                                </div>
                                <input @input="form.file = $event.target.files[0]" type="file" accept=".pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah file. Hanya file PDF (Max 5MB).</p>
                                <div v-if="form.errors.file" class="text-red-500 text-sm mt-1">{{ form.errors.file }}</div>
                                <progress v-if="form.progress" :value="form.progress.percentage" max="100" class="mt-2 w-full h-2 rounded overflow-hidden bg-gray-200">
                                    {{ form.progress.percentage }}%
                                </progress>
                            </div>
                        </div>

                        <!-- Perihal (Full width) -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700">Perihal</label>
                            <textarea v-model="form.subject" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Link :href="route('outgoing-letters.index')" class="mr-3 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</Link>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700" :disabled="form.processing">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
