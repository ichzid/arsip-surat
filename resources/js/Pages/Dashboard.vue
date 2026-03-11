<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Card Surat Masuk -->
                    <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-lg p-6">
                    <div class="text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Surat Masuk</h3>
                        <div class="text-3xl font-bold text-indigo-600">{{ stats.incoming }}</div>
                        <p class="text-sm text-gray-500 mt-2">Total surat masuk yang dapat diakses</p>
                        <div class="mt-4">
                            <Link :href="route('incoming-letters.index')" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Lihat Detail &rarr;</Link>
                        </div>
                    </div>
                </div>

                <!-- Card Surat Keluar -->
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-lg p-6">
                    <div class="text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Surat Keluar</h3>
                        <div class="text-3xl font-bold text-green-600">{{ stats.outgoing }}</div>
                        <p class="text-sm text-gray-500 mt-2">Total surat keluar yang tercatat</p>
                        <div class="mt-4">
                            <Link :href="route('outgoing-letters.index')" class="text-green-600 hover:text-green-800 text-sm font-medium">Lihat Detail &rarr;</Link>
                        </div>
                    </div>
                </div>

                <!-- Card Disposisi Pending -->
                <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-lg p-6">
                    <div class="text-gray-900">
                        <h3 class="text-lg font-semibold mb-2">Disposisi Pending</h3>
                        <div class="text-3xl font-bold text-yellow-600">{{ stats.pending_dispositions }}</div>
                        <p class="text-sm text-gray-500 mt-2">Disposisi yang perlu ditindaklanjuti</p>
                        <div class="mt-4">
                            <Link :href="route('incoming-letters.index')" class="text-yellow-600 hover:text-yellow-800 text-sm font-medium">Lihat Surat &rarr;</Link>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-gray-100 rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Selamat Datang di Sistem Arsip Surat</h3>
                    <p>
                        Sistem ini digunakan untuk mengelola surat masuk, surat keluar, dan disposisi antar divisi.
                        Silakan gunakan menu navigasi di sebelah kiri untuk mengakses fitur yang tersedia.
                    </p>
                </div>
            </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
