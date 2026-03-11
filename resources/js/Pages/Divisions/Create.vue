<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    code: '',
    description: '',
    active: true,
});

const submit = () => {
    form.post(route('divisions.store'));
};
</script>

<template>
    <Head title="Tambah Divisi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Tambah Divisi Baru
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit">
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Kode Divisi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Kode Divisi</label>
                                <input v-model="form.code" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <div v-if="form.errors.code" class="text-red-500 text-sm mt-1">{{ form.errors.code }}</div>
                            </div>

                            <!-- Nama Divisi -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nama Divisi</label>
                                <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <div v-if="form.errors.name" class="text-red-500 text-sm mt-1">{{ form.errors.name }}</div>
                            </div>

                            <!-- Keterangan -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>

                            <!-- Status -->
                            <div class="flex items-center">
                                <input v-model="form.active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <label class="ml-2 block text-sm text-gray-900">Aktif</label>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <Link :href="route('divisions.index')" class="mr-3 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Batal</Link>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700" :disabled="form.processing">
                                Simpan Divisi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
