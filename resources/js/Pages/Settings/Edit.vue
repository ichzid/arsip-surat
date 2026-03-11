<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    settings: Object,
});

const form = useForm({
    app_name: props.settings.app_name || '',
    app_description: props.settings.app_description || '',
    app_logo: null,
    institution_name: props.settings.institution_name || '',
    institution_address: props.settings.institution_address || '',
    institution_email: props.settings.institution_email || '',
    institution_phone: props.settings.institution_phone || '',
});

const submit = () => {
    form.post(route('settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Show success message or reload if needed
        },
    });
};
</script>

<template>
    <Head title="Pengaturan Website" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Pengaturan Website
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <form @submit.prevent="submit" enctype="multipart/form-data">
                    
                    <!-- Identitas Aplikasi -->
                    <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Identitas Aplikasi</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Pengaturan tampilan nama dan logo aplikasi.
                            </p>
                        </header>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Aplikasi</label>
                                <input v-model="form.app_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
                                <input v-model="form.app_description" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Logo (Aplikasi & Instansi)</label>
                                <div class="mt-2 flex items-center gap-x-3">
                                    <img v-if="props.settings.app_logo" :src="props.settings.app_logo" class="h-12 w-12 text-gray-300" />
                                    <input @input="form.app_logo = $event.target.files[0]" type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Identitas Instansi -->
                    <div class="mt-6 p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">Identitas Instansi</h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Informasi instansi untuk kop surat dan laporan.
                            </p>
                        </header>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Nama Instansi</label>
                                <input v-model="form.institution_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                                <textarea v-model="form.institution_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email Instansi</label>
                                <input v-model="form.institution_email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Telepon Instansi</label>
                                <input v-model="form.institution_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Simpan Pengaturan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
